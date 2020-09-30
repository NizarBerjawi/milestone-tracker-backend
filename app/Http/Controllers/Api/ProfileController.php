<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProfile;
use App\Http\Resources\ProfileResource;

/**
 * @group  Profile
 *
 * Endpoints for user profiles
 */
class ProfileController extends Controller
{
    /**
     * Create a new ProfileController instance.
     * 
     * @return void
     */
    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    /**
     * Create a new profile.
     * 
     * @param  StoreProfile  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProfile $request)
    {
        if ($request->user()->hasProfile()) {
            return response()->json([
                'message' => 'You have already created a profile'
            ], 422);
        }

        $data = $request->only(['first_name', 'last_name']);

        $profile = new Profile($data);
        $profile->user()->associate($request->user());
        $profile->save();

        return ProfileResource::make($profile)
            ->additional([
                'message' => 'Profile saved successfully'
            ]);
    }
}
