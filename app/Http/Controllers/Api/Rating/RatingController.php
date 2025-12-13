<?php

namespace App\Http\Controllers\Api\Rating;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use PhpParser\Builder\Function_;

class RatingController extends Controller
{

  public function GetAllRatings()
{
    $user = User::where('id',Auth::id())->first();
    
    $ratings = $user->ratings()->with('images','owner')->get(); 
    return response()->json(['ratings' => $ratings], 200);
}

    public function storerate(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|integer|exists:apartments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            
        ]);

       
        $user = User::where('id',Auth::id())->first();
        
        if($user->ratings()->where('apartment_id', $request->apartment_id)->exists()){
            return response()->json(['message' => 'You have already rated this apartment.'], 400);
        }

      $rating = $user->ratings()->attach($request->apartment_id, [
    'rating' => $request->rating, 
    'comment' => $request->comment,
]);
        
        return response()->json(['message' => 'Rating submitted successfully.','TheRating'=>$rating], 201);
    }


    public function updateRating(Request $request)
    {
     
        $request->validate([
            'apartment_id' => 'required|integer|exists:apartments,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $user = User::where('id',Auth::id())->first();

        $existingRating = $user->ratings()
        ->where('apartment_id', $request->apartment_id)
        ->first()
        ->with('images','owner')->get() ;

        if (!$existingRating) {
            return response()->json(['message' => 'No existing rating found for this apartment.'], 404);
        }

       $user->ratings()->updateExistingPivot($request->apartment_id, [
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
$newRating = $user->ratings()->where('apartment_id', $request->apartment_id)->first();
        return response()->json(['message' => 'Rating updated successfully.','TheNewRating'=>$newRating], 200);
    }

    public function destroyRating(Request $request )
    {
        $request->validate([
            'apartment_id' => 'required|integer|exists:apartments,id',
        ]);
    
        $user = User::where('id',Auth::id())->first();
        
        $existingRating = $user->ratings()->where('apartment_id', $request->apartment_id)->first();

        if (!$existingRating) {
            return response()->json(['message' => 'No existing rating found for this apartment.'], 404);
        }

        $user->ratings()->detach($request->apartment_id);
        return response()->json(['message' => 'Rating deleted successfully.'], 200);

    }

}