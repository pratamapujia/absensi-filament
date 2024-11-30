<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            /**
             * Email
             * 
             * @example abi@mail.com
             */
            'email' => 'required|email',

            /**
             * Password
             * 
             * @example password
             */
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'The provided credentials are incorrect.'
            ], 422);
        }

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ],
            'message' => 'Login Successful'
        ]);
    }
}
