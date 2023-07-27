<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function getProfiles() {
        return response()->json([
            'profiles' => Profile::where('deleted_at', null)->get()
        ], 200);
    }

    public function storeProfile(Request $req) {
        $ucName = ucwords(strtolower($req->name));
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:profiles', 'max:255']
        ],[
            'name.required' => 'Name is required.',
            'name.unique' => 'The name '.$ucName.' is already exist.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $existingProfiles = Profile::where('is_default', true)->get();

        foreach ($existingProfiles as $ep) {
            $ep->update([
                'is_default' => false 
            ]);
        }

        $profile = new Profile;
        $profile->name = $ucName;
        $profile->is_default = true;
        $profile->save();

        return response()->json([
            'profile' => $profile
        ], 200);
    }

    public function updateProfile(Request $req) {
        $ucName = ucwords(strtolower($req->name));
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:profiles', 'max:255']
        ],[
            'name.required' => 'Name is required.',
            'name.unique' => 'The name '.$ucName.' is already exist.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $profile = Profile::find($req->profileId);
        $profile->update([
            'name' => $ucName
        ]);

        return response()->json([
            'success' => 'Profile successfully updated.',
            'profile' => $profile
        ]);
    }

    public function deleteProfile(Request $req) {
        $profile = Profile::find($req->profileId);

        if ($profile->is_default) {
            $profile->update(['is_default' => false]);
            $oldestProfile = Profile::where('deleted_at', null)->oldest()->first();
            $oldestProfile->update([
                'is_default' => true
            ]);
        }

        $profile->delete();

        return response()->json([
            'success' => 'Profile successfully deleted.',
            'profile' => $profile
        ]);
    }
}
