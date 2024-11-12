<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiLoginRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Http\Requests\Api\LoginUserRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Permissions\V1\Abilities;

class AuthController extends Controller
{
    use ApiResponses;

    /**
     * Login
     *
     * Authenticates the User and returns the API token.
     *
     * @unauthenticated
     * @group Authentication
     * @response 200 {
    "data": {
        "token": "{YOUR_AUTH_KEY}"
    },
    "message": "Authenticated",
    "status": 200
}
     */
    public function login(LoginUserRequest $request) {
        $request->validated($request->all());

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid credentials', 401);
        }

        $user = User::firstWhere('email', $request->email);

        return $this->ok(
            'Authenticated',
            [
                'token' => $user->createToken(
                    'API token for ' . $user->email,
                    Abilities::getAbilities($user),
                    now()->addMonth())->plainTextToken
            ]
        );
    }


    /**
     * Logout
     *
     * Signs out the user and destroys the API token.
     *
     * @group Authentication
     * @response 200 {}
     */
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return $this->ok('');
    }

    // public function register() {
    //     return $this->ok('register');
    // }
}
