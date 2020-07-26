<?php

namespace App\Http\Controllers\Api;

use App\Models\Profile;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShowProfile;
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
     */
    public function __construct() 
    {
        $this->middleware('auth:api');
    }

    /**
     * 
     */
    public function index()
    {
        $profiles = Profile::with('user')->get();

        return ProfileResource::collection($profiles);
    }

    /** 
     * 
     */
    public function show(ShowProfile $request, Profile $profile) 
    {
        return new ProfileResource($profile);
    }

    /**
     * 
     */
    public function store(StoreProfile $request)
    {
        if ($request->user()->hasProfile()) {
            return response()->json([
                'message' => 'You have already created a profile.'
            ], 422);
        }

        $data = $request->only(['first_name', 'last_name']);

        $profile = new Profile($data);
        $profile->user()->associate($request->user());
        $profile->save();
        
        return new ProfileResource($profile);
    }
}
