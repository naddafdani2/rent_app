<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }


    public function show(User $user)
    {
        return response()->json([
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $fields = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'personal_photo' => 'sometimes|image|max:2048',
            'id_photo' => 'sometimes|image|max:2048',
            'birth_date' => 'sometimes|date',
            'phone' => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8'
        ]);

        // store the user's photos in a specific folder (app/public/...)
        // and save their(photos) names in user record at the database

        if ($request->hasFile('personal_photo')) {

            if ($user->personal_photo) {
                Storage::disk('public')->delete("personal_photos/$user->personal_photo");
                }
        
            $file = $request->file('personal_photo');
            $filename = "user_{$user->id}_" . time() . "." . $file->extension();
            $file->storeAs("personal_photos", $filename, "public");
        
            $fields['personal_photo'] = $filename;
        }

        if ($request->hasFile('id_photo')) {

            if ($user->id_photo) {
                Storage::disk('public')->delete("id_photos/$user->id_photo");
                }
        
            $file = $request->file('id_photo');
            $filename = "user_{$user->id}_" . time() . "." . $file->extension();
            $file->storeAs("id_photos", $filename, "public");
        
            $fields['id_photo'] = $filename;
        }

        $user->update($fields);
    
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}