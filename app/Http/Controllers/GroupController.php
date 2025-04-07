<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array|min:1', 
            'group_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
    
        $user = Auth::user();
        $group = new Group();
    
        $group->name = $request->name;
        $group->created_by = $user->id;
    
        if ($request->hasFile('group_image')) {
            $file = $request->file('group_image');
            $fileName = time() . '.' . $file->getClientOriginalExtension(); 
            $filePath = $file->storeAs('group_images', $fileName, 'public');
    
            $group->group_img = $filePath;
        }
    
        $group->save();
    
        $selectedUsers = $request->input('users'); 
        $group->users()->attach($selectedUsers);
    
  
        $group->users()->attach($user->id);
    
        return response()->json([
            'success' => true,
            'group' => [
                'id' => $group->id,
                'name' => $group->name,
                'image' => $group->group_img ? asset('storage/' . $group->group_img) : null
            ]
        ]);
    }
    
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();
    
        return response()->json([
            'status' => 'success'
        ]);
    }
    

    
    
}
