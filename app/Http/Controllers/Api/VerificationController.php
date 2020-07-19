<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Http\Requests\Auth\ResendVerificationRequest;

/**
 * @group  Verification
 *
 * Endpoints for verifying users
 */
class VerificationController extends Controller
{
    /**
     * Verify email
     *
     * Verify a newly registered user's email.
     *
     * The user will receive an email with the required verification
     * URL.
     *
     * @urlParam id required The id of the user being verified
     * @queryParam expires required The time when the url expire
     * @queryParam hash required The sha1 hash of a string
     * @queryParam signature required A string containing a calculated message digest as lowercase hexits
     *
     * @param RegisterRequest  $request
     * @param string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function verify(Request $request, $id)
    {
        if (! $request->hasValidSignature()) {
            return response()->json(['message' => 'This email confirmation link has expired.'], 410);
        }

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'You have already verified your email.'], 422);
        }

        if (! $user->markEmailAsVerified()) {
            return response()->json(['message' => 'Failed to verify your email.'], 400);
        }

        event(new Verified($user));

        return response()->json(['message' => 'Your email was verified']);
    }

    /**
     * Resend a verification email
     *
     * A user can request a new verification email to be sent
     * in case the previous email expired.
     *
     * @param ResendVerificationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(ResendVerificationRequest $request)
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'You have already verified your email.'], 422);
        }

        try {
            $user->sendEmailVerificationNotification();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Something went wrong. Please try again later.'
            ], 400);
        }

        return response()->json([
            'message' => 'A verification link was sent to your email.',
        ], 200);
    }
}
