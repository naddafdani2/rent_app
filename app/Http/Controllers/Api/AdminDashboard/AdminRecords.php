<?php

namespace App\Http\Controllers\Api\AdminDashboard;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Apartment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRecords extends Controller
{
    public function GetAllRecords()
    {
        if(Auth::check())
        {
            $user = User::where('id',Auth::id())->first();

            if($user->phone !=='00000000'&&$user->password !=='00000000')
            {
                return response()->json(['message' => 'This User is Not The Admin'], 403);
            }else{
                $records = Admin::all();
                return response()->json(['records' => $records], 200);  
            }
    }
}

public function GetRecordsByType(Request $request,$targetType)
{
    if(Auth::check())
    {
        $user = User::where('id',Auth::id())->first();

        if($user->phone !=='00000000'&&$user->password !=='00000000')
        {
            return response()->json(['message' => 'This User is Not The Admin'], 403);  
            
        }else{
            $records = Admin::where('target_type',$targetType)->get();
            return response()->json(['records' => $records], 200);  
        
        }
}}

public function updateRecord(Request $request,$recordId)
{

    if(Auth::check())
    {
        $user = User::where('id',Auth::id())->first();

        if($user->phone !=='00000000'&&$user->password !=='00000000')
        {
            return response()->json(['message' => 'This User is Not The Admin'], 403);
        }
        else{
            $record = Admin::findOrFail($recordId);

            if($record->target_type == 'apartment')
            {
                $request->validate([
                    'is_available' => 'required|boolean',
                    'target_type'=>'apartment',
                    'target_id'=>'required|integer|exists:apartments,id',
                    'action'=>'required|string',
                    'reason'=>'nullable|string',
                ]);

                $apartment = Apartment::findOrFail($record->target_id);
                $apartment->is_available = $request->is_available;
                $apartment->save();

                $record->update([
                    'is_available' => $request->is_available,
                    'action' => $request->action,
                    'target_type'=>'apartment',
                    'target_id'=>$record->target_id,
                    'reason' => $request->reason,
                ]);

                return response()->json(['message' => 'Record and Apartment condition updated successfully','record'=>$record], 200);
            }else if($record->target_type == 'user')
            {
                $request->validate([
                    'is_approved' => 'required|boolean',
                    'target_type'=>'user',
                    'target_id'=>'required|integer|exists:users,id',
                    'action'=>'required|string',
                    'reason'=>'nullable|string',
                ]);

                $userTarget = User::findOrFail($record->target_id);
                $userTarget->is_approved = $request->is_approved;
                $userTarget->save();

                $record->update([
                    'is_approved' => $request->is_approved,
                    'action' => $request->action,
                    'target_type'=>'user',
                    'target_id'=>$record->target_id,
                    'reason' => $request->reason,
                ]);

                return response()->json(['message' => 'Record and User approval status updated successfully','record'=>$record], 200);
        }
}}
}


public function deleteRecord($recordId)
{
    if(Auth::check())
    {
        $user = User::where('id',Auth::id())->first();

        if($user->phone !=='00000000'&&$user->password !=='00000000')
        {
            return response()->json(['message' => 'This User is Not The Admin'], 403);
            
        }else{
            $record = Admin::findOrFail($recordId);
            $record->delete();

            return response()->json(['message' => 'Record deleted successfully'], 200);  
        }

}}}