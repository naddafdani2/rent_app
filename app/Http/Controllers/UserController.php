<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
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

        if (isset($fields['password'])) {
            $fields['password'] = Hash::make($fields['password']);
        }

        $user->update($fields);
    
        return $user;
    }

    public function destroy(User $user)
    {
        $user->delete();
    }
}