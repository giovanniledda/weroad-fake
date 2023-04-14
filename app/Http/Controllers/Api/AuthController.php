<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function response;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()
                ->json(['message' => 'Unauthorized: email or password are invalid'], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();

        $token = $user->createToken('Api token for user '.$user->id)->plainTextToken;

        return response()
            ->json([
                'data' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
    }

    // method for user logout and delete token
    public function logout()
    {
        auth('sanctum')->user()->tokens()->delete();

        return response()
            ->json([
                'message' => 'You have successfully logged out and the token was successfully deleted',
            ]);
    }
}
