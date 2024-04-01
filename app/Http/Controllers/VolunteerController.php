<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Volunteer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class VolunteerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $volunteers = Volunteer::with('user')->get();
        return response()->json(['volunteers' => $volunteers], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function myPosits()
    {
        $user = JWTAuth::user();
        $user = User::with('volunteer')->where('id', $user->id)->first();

        $myPosits = DB::table('posits')
            ->join('listings', 'posits.listing_id', '=', 'listings.id')
            ->where('volunteer_id', $user->volunteer->id)
            ->get();
        return response()->json(['myPosits' => $myPosits]);
    }
}
