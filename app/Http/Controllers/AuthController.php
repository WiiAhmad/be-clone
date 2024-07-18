<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->attempt($credentials)) {
            $user = auth()->user();
            $token = $user->createToken($user->name);
            return [
                'user' => $user,
                'token' => $token->plainTextToken
            ];
        }
        return response()->json([
            'message' => 'Unauthorized'
        ], 401);
    }

    public function logout(Request $request)
    {
        // $request->user()->token()->delete();

        // return [
        //     'message' => 'Successfully logged out'
        // ];
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create($data);

        //$token = $user->createToken('authToken')->accessToken;

        $token = $user->createToken($request->name);

        return[
            'user' => $user,
            'token' => $token->plainTextToken
        ];
    }
}
