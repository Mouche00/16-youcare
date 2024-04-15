<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Organizer;
use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class ListingController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/listing",
     *     tags={"Listing"},
     *     operationId="listing-all",
     *     summary="Fetch all listings",
     *     @OA\Parameter(
     *          name="query",
     *          in="query",
     *          description="Query",
     *          @OA\Schema(type="string")
     *      ),
     *     @OA\Response(response="200", description="Listings fetched successfully")    
     * )
     */
    
    public function index(Request $request)
    {
        $listings = null;
        try{
            if(JWTAuth::user() !== null) {
                $userId = JWTAuth::user()->id;
                $listings = Listing::latest()->whereHas('volunteer', function($q) use ($userId){
                    return $q->where('volunteers.id', '=', $userId);
                })->orDoesntHave('volunteer')->paginate(6);
            } else {
                $listings = Listing::latest()->paginate(6);
            }

            if ($request->input('query')) {
                $listings = Listing::whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($request->input('query')) . '%'])
                    ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($request->input('query')) . '%'])
                    ->paginate(6);
            }
            return response()->json(['listings' => $listings], 200);
        }
        catch(\Exception $e){
            return response()->json([
                'case' => 'failed',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function myListings()
    {
        try{
            $user = JWTAuth::user();
            $listings = Listing::where('organizer_id',$user->organizer()->first()->id)
                ->get();

            return response()->json([
                'case' => 'success',
                'listings' => $listings
            ]);

        }
        catch(\Exception $e){
            return response()->json([
               'case' => 'failed',
               'message' => $e->getMessage()
            ]);
        }

    }

    /**
     * @OA\Post(
     *     path="/api/listing",
     *     tags={"Listing"},
     *     operationId="listing-add",
     *     summary="Create a new listing",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Listing's title",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Listing's description",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Listing's date",
     *         required=true,
     *         @OA\Schema(type="date")
     *     ),
     *     @OA\Parameter(
     *          name="location",
     *          in="query",
     *          description="Listing's location",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="skills",
     *          in="query",
     *          description="Listing's skills",
     *          required=true,
     *          @OA\Schema(type="json")
     *      ),
     *   @OA\Response(
     *      response=200,
     *       description="Listing added successfully",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not Found"
     *   ),
     * @    OA\Response(
     *           response=403,
     *           description="Forbidden"
     *       ),
     *   @OA\Response(
     *      response="422",
     *      description="Validation errors"
     *   ),
     *  security={{ "apiAuth": {} }}
     *)
     **/

    public function store(Request $request)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'skills' => 'required|array'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = JWTAuth::user();
        //        $listing = $user->organizer()->first()->listing()->create($request->all());
        $listing = Listing::create(array_merge($request->all(), [
            'organizer_id' => $user->organizer()->first()->id
        ]));
        return response()->json(['listing' => $listing, 'message' => 'Listing Added Succefully'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Listing $listing)
    {
        //
        return response()->json($listing);
    }

    /**
     * @OA\Put(
     *     path="/api/listing/{listing}",
     *     tags={"Listing"},
     *     operationId="listing-update",
     *     summary="Update a new listing",
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Listing's title",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="description",
     *         in="query",
     *         description="Listing's description",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Listing's date",
     *         @OA\Schema(type="date")
     *     ),
     *     @OA\Parameter(
     *          name="location",
     *          in="query",
     *          description="Listing's location",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="skills",
     *          in="query",
     *          description="Listing's skills",
     *          @OA\Schema(type="json")
     *      ),
     *   @OA\Response(
     *      response=200,
     *       description="Listing updated successfully",
     *      @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not Found"
     *   ),
     * @    OA\Response(
     *           response=403,
     *           description="Forbidden"
     *       ),
     *   @OA\Response(
     *      response="422",
     *      description="Validation errors"
     *   ),
     *  security={{ "apiAuth": {} }}
     *)
     **/
    public function update(Request $request, Listing $listing)
    {
        //
        $rules = [
            'title' => 'string|max:255',
            'description' => 'string|max:255',
            'date' => 'date',
            'location' => 'string|max:255',
            'skills' => 'array'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $organizer = JWTAuth::user()->organizer()->first();
        $listing = $organizer->listings()->find($listing->id);

        if( ! $listing == null) {
            $listing->update($request->all());
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized action'
            ], 403);
        }

        return response()->json(['listing' => $listing, 'message' => 'Listing updated Succefully'], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/apply",
     *     tags={"Application"},
     *     operationId="application-add",
     *     summary="Add a new application",
     *     @OA\Parameter(
     *         name="listing_id",
     *         in="query",
     *         description="Listing's id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Application added successfully",
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not Found"
     *   ),
     *   @OA\Response(
     *           response=403,
     *           description="Forbidden"
     *   ),
     *  security={{ "apiAuth": {} }}
     *)
     **/

    public function apply(Request $request)
    {
        $user = JWTAuth::user();
        $listing = Listing::findOrFail($request->listing_id);
        $volunteer = Volunteer::where('user_id', $user->id)->first();
        $listing->volunteer()->attach($volunteer->id);
        return response()->json([$listing, 'message' => 'you have applied succefully']);
    }

    public function listingRequest()
    {
        $user = JWTAuth::user();
        $applications = DB::table('applications')
            ->join('listings', 'applications.listing_id', '=', 'listings.id')
            ->join('volunteers', 'applications.volunteer_id', '=', 'volunteers.id')
            ->join('users', 'volunteers.user_id', '=', 'users.id')
            ->where('listings.organizer_id', $user->organizer()->first()->id)
            ->where('applications.status', 'pending')
            ->select('applications.*', 'applications.id as application_id', 'listings.*', 'volunteers.*', 'users.*')
            ->get();
        return response()->json($applications);
    }

       /**
     * @OA\Put(
     *     path="/api/approve",
     *     tags={"Application"},
     *     operationId="application-approve",
     *     summary="Approve an application",
     *     @OA\Parameter(
     *         name="application_id",
     *         in="query",
     *         description="Application's id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Application approved successfully",
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not Found"
     *   ),
     *   @OA\Response(
     *           response=403,
     *           description="Forbidden"
     *   ),
     *  security={{ "apiAuth": {} }}
     *)
     **/

    public function approve(Request $request)
    {
        $posit = DB::table('applications')
            ->where('id', $request->application_id)
            ->update(['status' => 'approved']);

        return response()->json(['posit_id' => $posit, 'message' => 'The application has been successfully approved.'], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/decline",
     *     tags={"Application"},
     *     operationId="application-decline",
     *     summary="Decline an application",
     *     @OA\Parameter(
     *         name="application_id",
     *         in="query",
     *         description="Application's id",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *   @OA\Response(
     *      response=200,
     *       description="Application declined successfully",
     *   ),
     *   @OA\Response(
     *      response=400,
     *      description="Bad Request"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="Not Found"
     *   ),
     *   @OA\Response(
     *           response=403,
     *           description="Forbidden"
     *   ),
     *  security={{ "apiAuth": {} }}
     *)
     **/

    public function decline(Request $request)
    {
        $posit = DB::table('applications')
            ->where('id', $request->application_id)
            ->update(['status' => 'declined']);
        return response()->json(['posit_id' => $posit, 'message' => 'The application has been successfully declined.']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Listing $listing)
    {
        //
        $listing->delete();
        return response()->json(['listing' => $listing, 'message' => 'Listing Deleted Succefully'], 200);
    }
}
