<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ToDo;
use App\Models\Profile;

class ToDoController extends Controller
{
    public function getToDoList(Request $req) {
        $defaultProfile = Profile::where('is_default', true)->first();
        $selectedProfile = Profile::where('id', $req->profileId)->first();

        $currentProfile = $req->profileId ? $selectedProfile->id : $defaultProfile->id;
        
        $toDos = ToDo::where('profile_id', $currentProfile)->get();

        if ($toDos->isEmpty()) {
            return response()->json([
                'message' => 'No To Do list for the selected profile.'
            ], 200);
        }

        return response()->json([
            'todos' => $toDos
        ], 200);
    }

    public function storeToDo(Request $req) {
        $validator = Validator::make($req->all(), [
            'profile_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:255']
        ],[
            'profile_id.required' => 'Profile is required.',
            'title.required' => 'Title is required.',
            'description.required' => 'Description is required.',
            'title.max' => 'The max length of the title is 60 characters only.',
            'description.max' => 'The max length of the description is 255 characters only.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ]);
        }

        $toDo = new ToDo;
        $toDo->profile_id = $req->profile_id;
        $toDo->title = $req->title;
        $toDo->description = $req->description;
        $toDo->save();

        return response()->json([
            'success' => 'Your to do is successfully created.',
            'todo' => $toDo
        ], 200);
    }

    public function updateToDo(Request $req) {
        
    }
}
