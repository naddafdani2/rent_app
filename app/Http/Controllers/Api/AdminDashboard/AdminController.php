<?php

namespace App\Http\Controllers\Api\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function apartmentsConditions(Request $request,$apartmentId)
    {
        $request->validate([
            'is_available' => 'required|boolean',
            'target_type'=>'apartment',
            'target_id'=>'required|integer|exists:apartments,id',
            'action'=>'required|string',
            'reason'=>'nullable|string',
        ]);

     if(Auth::check())
        {
            $user = User::where('id',Auth::id())->first();

            if($user->phone !=='00000000'&&$user->password !=='00000000'){
                return response()->json(['message' => 'This User is Not The Admin'], 403);
            }else{
                 $apartment = Apartment::findOrFail($apartmentId);
    
           if(!$apartment) {
            return response()->json(['message' => 'Apartment not found'], 404);
        }

    $apartment->is_available = $request->is_available;
    $apartment->save();

    Admin::create([ 
        'target_type' => 'apartment', 
        'target_id'   => $apartmentId, 
        'action'      => $request->action, 
        'reason'      => $request->reason,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);
    
    return response()->json([
        'message' => 'Apartment condition updated successfully',
        'apartment' => $apartment
    ], 200);    
            }
        }   
        
          
    }


    public function usersConditions(Request $request,$userId)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
            'target_type'=>'user',
            'target_id'=>'required|integer|exists:users,id',
            'action'=>'required|string',
            'reason'=>'nullable|string',
        ]);

     if(Auth::check())
        {
            $user = User::where('id',Auth::id())->first();

            if($user->phone !=='00000000'&&$user->password !=='00000000')
            {
                return response()->json(['message' => 'This User is Not The Admin'], 403);
            }else{
                    $targetUser = User::findOrFail($userId);
                    $targetUser->is_approved = $request->is_approved;
                    $targetUser->save();
                    
                    Admin::create([ 
                        'target_type' => 'user', 
                        'target_id'   => $userId, 
                        'action'      => $request->action, 
                        'reason'      => $request->reason,
                    ]);

                    return response()->json([
                        'message' => 'User condition updated successfully',
                        'user' => $targetUser
                    ], 200);
            }       
}
    }


    public function adminApartmentsIndex()
{
    $apartments = Apartment::with('images', 'owner')->get();

    return response()->json([
        'success' => true,
        'data' => $apartments
    ], 200);
}

public function AdminUsersIndex()
{
    
    $users = User::latest()->get();

    return response()->json([
        'success' => true,
        'count'   => $users->count(),
        'data'    => $users
    ], 200);
}
}