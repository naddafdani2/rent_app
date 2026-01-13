<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request){

        $fields = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'personal_photo' => 'required|image|max:2048',
            'id_photo' => 'required|image|max:2048',
            'birth_date' => 'required|date',
            'phone' => 'required|string|max:20|unique:users,phone,',
            'email' => 'nullable|email|unique:users,email,',
            'password' => 'required|string|min:8'
        ]);

        //create the user in the database

        $user = User::create([
            'first_name' => $fields['first_name'],
            'last_name' => $fields['last_name'],
            'birth_date' => $fields['birth_date'],
            'phone' => $fields['phone'],
            'email' => $fields['email'] ?? null,
            'password' => $fields['password'],
        ]);

        //add the user's photos to database by update it

        $file = $request->file('personal_photo');
        $filename = "user_{$user->id}_" . time() . "." . $file->extension();
        $file->storeAs("personal_photos", $filename, "public");

        $fields['personal_photo'] = $filename;


        $file = $request->file('id_photo');
        $filename = "user_{$user->id}_" . time() . "." . $file->extension();
        $file->storeAs("id_photos", $filename, "public");
        
        $fields['id_photo'] = $filename;


        $token = $user->createToken($request->first_name);
        
        app(UserController::class)->update($request,$user);
     
         return response($token->plainTextToken);

        }


        
    public function login(Request $request){
            
            $request->validate([
                'phone' => 'required|string|max:20|exists:users',
                'email' => 'nullable|email|exists:users',
                'password' => 'required|string|min:8'
            ]);
            
            //search if the email is exist and the password is correct
            
            $user=User::where('email',$request->email)->first();
            
            if(!$user | !Hash::check($request->password,$user->password)){
                return "the password is incorrect";
            }

            $token = $user->createToken($user->first_name);
            
            return response($token->plainTextToken);

    }



    public function logout(Request $request){

        $request->user()->tokens()->delete();
        
        return "you are logged out";

    }
}
