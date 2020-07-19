<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @group  Authentication
 *
 * Endpoints for authenticating users
 */
class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register an account
     * 
     * Allows a user to register an account.

     * After registration, a confirmation email will be sent to the provided email.
     * 
     * @bodyParam email string required The email of the user Example: test@test.com
     * @bodyParam password string required The password of the user.
     * @bodyParam password_confirmation string required The confirmation of the password
     * 
     * @param RegisterRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try  {
            DB::transaction(function () use ($request) {
                $user = User::create([
                    'email' => $request->input('email'),
                    'password' => bcrypt($request->input('password'))
                ]);

                $user->sendEmailVerificationNotification();
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 400);
        }

        return response()->json([
            'message' => 'A verification link was sent to your email.',
        ], 200);
    }

    /**
     * Get a JWT
     *
     * Get a JSON Web Token after submitting email and password
     * 
     * @bodyParam email string required The email of the user Example: test@test.com
     * @bodyParam password string The password of the user.
     * 
     * @param LoginRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'This email or password does not exist.'], 401);
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Your email address is not verified.'], 403);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User
     *
     * Returns data related to the authenticated user
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return new UserResource(auth()->user());
    }

    /**
     * Log the user out
     *
     * Invalidate the JWT token
     * 
     * @authenticated
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token
     *
     * Provides a new JWT and invalidates the old one
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure
     *
     * @param  string $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
