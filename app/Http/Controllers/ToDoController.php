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

        $currentProfile = $req->profileId !== 'undefined' ? $selectedProfile->id : $defaultProfile->id;
        
        $toDos = ToDo::where([
            ['profile_id', $currentProfile],
            ['deleted_at', null]
        ])->get();

        return response()->json([
            'todos' => $toDos
        ], 200);
    }

    public function storeToDo(Request $req) {
        $validator = Validator::make($req->all(), [
            'profileId' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:60'],
            'description' => ['nullable', 'string', 'max:255']
        ],[
            'profileId.required' => 'Profile is required.',
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
        $toDo->profile_id = $req->profileId;
        $toDo->title = $req->title;
        $toDo->description = $req->description;
        $toDo->save();

        return response()->json([
            'success' => 'Your To Do is successfully created.',
            'todo' => $toDo
        ], 200);
    }

    public function updateToDo(Request $req) {
        $validator = Validator::make($req->all(), [
            'title' => ['required', 'string', 'max:60'],
            'description' => ['required', 'string', 'max:255']
        ],[
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

        $toDo = ToDo::find($req->toDoId);
        $toDo->update([
            'title' => $req->title,
            'description' => $req->description
        ]);

        return response()->json([
            'success' => 'Your To Do is successfully updated.',
            'todo' => $toDo
        ], 200);
    }

    public function deleteTodo(Request $req) {
        $toDo = ToDo::find($req->toDoId);
        $toDo->delete();

        return response()->json([
            'success' => 'Your To Do successfully deleted.',
            'todo' => $toDo
        ]);
    }
}
