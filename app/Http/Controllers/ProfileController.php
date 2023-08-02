<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Profile;
use App\Models\ToDo;

class ProfileController extends Controller
{
    public function getProfiles() {
        return response()->json([
            'profiles' => Profile::where('deleted_at', null)->orderBy('is_default', 'desc')->orderBy('name', 'asc')->get(),
            'default' => Profile::where([
                ['deleted_at', null],
                ['is_default', true]
            ])->first()
        ], 200);
    }

    public function storeProfile(Request $req) {
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:profiles,name,NULL,id,deleted_at,NULL', 'max:30']
        ],[
            'name.required' => 'Profile name is required.',
            'name.unique' => 'Profile name is already exist.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

        $existingProfiles = Profile::where('is_default', true)->get();

        foreach ($existingProfiles as $ep) {
            $ep->update([
                'is_default' => false 
            ]);
        }

        $profile = new Profile;
        $profile->name = $req->name;
        $profile->is_default = true;
        $profile->save();

        return response()->json([
            'success' => 'Profile successfully created.',
            'profile' => $profile
        ], 200);
    }

    public function updateProfile(Request $req) {
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:profiles,name,'.$req->profileId.',id,deleted_at,NULL', 'max:30']
        ],[
            'name.required' => 'Profile name is required.',
            'name.unique' => 'Profile name is already exist.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->all()
            ]);
        }

        $profile = Profile::find($req->profileId);
        $profile->update([
            'name' => $req->name
        ]);

        return response()->json([
            'success' => 'Profile successfully updated.',
            'profile' => $profile
        ], 200);
    }

    public function deleteProfile(Request $req) {
        $profile = Profile::find($req->profileId);

        if ($profile->is_default) {
            return response()->json([
                'error' => 'You cannot delete a default profile.'
            ]);
        }

        $toDos = ToDo::where('profile_id', $profile->id)->get();

        if ($toDos->isNotEmpty()) {
            foreach($toDos as $td) {
                $td->delete();
            }
        }

        $profile->delete();

        return response()->json([
            'success' => 'Profile successfully deleted.',
            'profile' => $profile
        ], 200);
    }

    public function makeDefault(Request $req) {
        $profile = Profile::find($req->profileId);
        $existingProfiles = Profile::where('is_default', true)->get();

        foreach ($existingProfiles as $ep) {
            $ep->update([
                'is_default' => false 
            ]);
        }

        $profile->update([
            'is_default' => true
        ]);

        return response()->json([
            'success' => 'Profile successfully marked as default.',
            'profile' => $profile
        ], 200);
    }
}
