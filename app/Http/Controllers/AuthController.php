<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return response()->json(['redirectUrl' => $redirectUrl]);
    }

    /**
     * Handle the Google callback and create or retrieve the user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')
            ->stateless()
            ->user();
        $user = User::firstOrCreate(
            ['email' => $googleUser->email],
            [
                'name' => $googleUser->name,
            ]
        );

        $apiToken = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'apiToken' => $apiToken,
        ]);
    }

    /**
     * Logout the user and invalidate the token.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logout successful']);
        }
        return response()->json(['message' => 'No user logged in'], 401);
    }

    /**
     * Get the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUser(Request $request)
    {
        $user = $request->user();
        if ($user) {
            return response()->json($user);
        }
        return response()->json(['message' => 'No user logged in'], 401);
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }
}
