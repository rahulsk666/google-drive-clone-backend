<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirect' => $redirectUrl]);
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')
            // ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->stateless()
            ->user();
        // dd($googleUser); // Debugging line to inspect the user object
        $token = $googleUser->token;
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
                'password' => \Hash::make(rand(100000, 999999))
            ]
        );

        $apiToken = $user->createToken('api-token')->plainTextToken;


        // dd($user); // Debugging line to inspect the user object
        return response()->json([
            'message' => 'Login successful',
            // 'user' => $user, // Adjust this to your desired redirect route
            'apiToken' => $apiToken,
            // 'googleToken' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // dd($request);
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logout successful']);
        }
        return response()->json(['message' => 'No user logged in'], 401);
    }

    public function getUser(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json($user);
        }
        return response()->json(['message' => 'No user logged in'], 401);
    }

    // get all users
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }
}
