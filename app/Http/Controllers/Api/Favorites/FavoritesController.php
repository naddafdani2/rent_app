<?php


namespace App\Http\Controllers\Api\Favorites;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorites;
use App\Models\Apartment;
use App\Models\User;

class FavoritesController extends Controller
{
public function GetAllFavorites(Request $request)
{         $user = User::where('id',Auth::id())->first();

    
    
    if (!$user) {
        return response()->json(['message' => 'Unauthorized or invalid token.'], 401);
    }
    
    $favorites = $user->favorites()->with('images','owner')->get();

    return response()->json($favorites, 200);
}



public function storefavorite(Request $request)
{
    $request->validate([
        'apartment_id' => 'required|exists:apartments,id',
    ]);

         $user = User::where('id',Auth::id())->first();


    if ($user->favorites()->where('apartment_id', $request->apartment_id)->exists()) {
         return response()->json(['message' => 'Apartment is already in favorites.'], 200);
    }
    
    $user->favorites()->attach($request->apartment_id);

    return response()->json(['message' => 'Apartment added to favorites successfully.'], 201);
}

public function removefavorite(Request $request)
{
    $request->validate([
        'apartment_id' => 'required|exists:apartments,id',
    ]);

         $user = User::where('id',Auth::id())->first();


    if (!$user->favorites()->where('apartment_id', $request->apartment_id)->exists()) {
         return response()->json(['message' => 'Apartment is not in favorites.'], 404);
    }
    
    $user->favorites()->detach($request->apartment_id);

    return response()->json(['message' => 'Apartment removed from favorites successfully.'], 200);  
}
}