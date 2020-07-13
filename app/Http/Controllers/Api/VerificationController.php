<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;

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
    public function send(Request $reques, User $user) 
    {
        dd('here');

        return response()->json(['message' => $user]);
    }
}