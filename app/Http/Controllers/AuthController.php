<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ApiResponse;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'bail|required|string',
            'email' => 'required|email',
            'password' => 'required|confirmed'
        ]);

        try {
            $result =  User::create(['username' => $request->username, 'email' => $request->email, 'password' => Hash::make($request->password)]);
        } catch (Exception $ex) {
            return ApiResponse::error('An excepted error occured while processing the request', 500);
        }
        return response()->noContent(201);
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|string|email', 'password' => 'required|string']);

        $user = User::where('email', $request->email)->first();

        if ($user  && Hash::check($request->password, $user->password)) {
            return response()->json(['token' => $user->createToken('auth_token')->plainTextToken]);
        } else {
            return ApiResponse::error('Bad credentails', 401);
        }
    }
}
