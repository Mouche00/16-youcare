<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = JWTAuth::user();

        $rules = [
            'name' => 'string|max:255',
            'email' => ['email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'password' => 'string|min:6|max:255'
        ];

        $validator = Validator::make($request->all(), $rules);
        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = array_merge(
            $request->all(),
            [
                'password' => bcrypt($request->password)
            ]
        );

        $user->update($data);

        return response()->json(['user' => $user, 'message' => 'User updated successfully'], 200);
    }
}
