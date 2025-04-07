<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }



    public function updateProfileimg(Request $request, $id)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $user = User::find($id);
        $group = Group::find($id);
    
        if ($user) {
            
            $file = $request->file('profile_picture');
            $filename = uniqid('user_') . '.' . $file->getClientOriginalExtension();
            $profilePath = $file->storeAs('profile_pictures', $filename, 'public');
    
            $user->update(['profile_picture' => $profilePath]);
    
            return response()->json([
                'status' => 'profile-updated',
                'type' => 'user',
                'image' => asset('storage/' . $profilePath)
            ]);
        } elseif ($group) {
           
            $file = $request->file('profile_picture');
            $filename = uniqid('group_') . '.' . $file->getClientOriginalExtension();
            $profilePath = $file->storeAs('group_images', $filename, 'public');
    
           
            $group->update(['group_img' => $profilePath]);
    
            return response()->json([
                'status' => 'profile-updated',
                'type' => 'group',
                'image' => asset('storage/' . $profilePath)
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'User or Group not found'
        ], 404);
    }
    
    

    
}
