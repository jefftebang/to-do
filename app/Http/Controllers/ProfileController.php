<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Profile;

class ProfileController extends Controller
{
    public function getProfiles() {
        return response()->json([
            'profiles' => Profile::where('deleted_at', null)->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get(),
            'default' => Profile::where([
                ['deleted_at', null],
                ['is_default', true]
            ])->first('id')
        ], 200);
    }

    public function storeProfile(Request $req) {
        $ucName = ucwords(strtolower($req->name));
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:profiles,name,NULL,id,deleted_at,NULL', 'max:50']
        ],[
            'name.required' => 'Profile name is required.',
            'name.unique' => 'Profile name is already exist.'
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
            'success' => 'Profile successfully created.',
            'profile' => $profile
        ], 200);
    }

    public function updateProfile(Request $req) {
        $ucName = ucwords(strtolower($req->name));
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:profiles,name,'.$req->profileId.',id,deleted_at,NULL', 'max:50']
        ],[
            'name.required' => 'Profile name is required.',
            'name.unique' => 'Profile name is already exist.'
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
            // $profile->update(['is_default' => false]);
            // $oldestProfile = Profile::where('deleted_at', null)->oldest()->first();
            // $oldestProfile->update([
            //     'is_default' => true
            // ]);
            return response()->json([
                'error' => 'You cannot deleted a default profile.'
            ]);
        }

        $profile->delete();

        return response()->json([
            'success' => 'Profile successfully deleted.',
            'profile' => $profile
        ]);
    }
}
