<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function getUsers() {
        return response()->json([
            'users' => User::all()
        ], 200);
    }

    public function storeUser(Request $req) {
        $nameLower = strtolower($req->name);
        $ucName = ucwords($nameLower);
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'unique:users', 'max:255']
        ], [
            '*.required' => 'Name is required.',
            'name.unique' => 'The name '.$ucName.' is already exist.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $existingUsers = User::where('is_default', true)->get();

        foreach ($existingUsers as $eu) {
            $eu->update([
                'is_default' => false 
            ]);
        }

        $user = new User;
        $user->name = $ucName;
        $user->is_default = true;
        $user->save();

        return response()->json([
            'user' => $user
        ]);
    }
}
