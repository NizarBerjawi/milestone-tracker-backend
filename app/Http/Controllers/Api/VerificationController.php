<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Http\Requests\Auth\ResendVerificationRequest;

class VerificationController extends Controller {
    /**
     *
     */
    public function __construct() {
        $this->middleware('signed')->only('verify');
    }

    /**
     *
     */
    public function verify(Request $request, $id)
    {
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
     *
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
