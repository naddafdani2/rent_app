<?php

namespace App\Http\Controllers\Api\Apartments;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Apt_image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class ApartmentController extends Controller
{
    // get all apartments
    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function index()
    {
        $apartments = Apartment::where('is_available', true)
            ->with('images', 'owner')
            ->get();

        return response()->json([$apartments, 200]);
    }

    // get single apartment
    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function show($id)
    {
        $apartment = Apartment::where('is_available', true)
        ->with('images', 'owner')->findOrFail($id);

        return response()->json([$apartment, 200]);
    }

    // create apartment
    /////////////////////////////////////////////////////////////////////////////////////////////////

   public function store(Request $request)
    {
        $request->validate([
            
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'total_area' => 'required|numeric',
            'price_per_day' => 'required|numeric',
            'price_per_month' => 'required|numeric',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'street' => 'required|string|max:255',
            'building_number' => 'required|integer',
            'level' => 'required|integer',

            'photos' => 'required|array|min:1', 
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5000', 
        ]);

        $data = $request->except('photos'); 
        $data['owner_id'] = Auth::id();
        

        $apartment = Apartment::create($data); 

        $imagesToSave = [];
        
        if ($request->hasFile('photos')) {
            
            foreach ($request->file('photos') as $index => $photo) {
                
                $path = Storage::disk('public')->put('apartment_photos', $photo);
                
                $imagesToSave[] = new Apt_image([
                    'image_path' => $path,
                    
                    'is_primary' => ($index === 0) 
                ]);
            }
            
            $apartment->images()->saveMany($imagesToSave);
        }

        return response()->json([
            'message' => 'The Apartment Added Successfully and Images Processed',
            'apartment' => $apartment->load('images') 
        ], 201);
    }


    // update apartment
    /////////////////////////////////////////////////////////////////////////////////////////////////
public function update(Request $request, $id)
    {
        
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'total_area' => 'sometimes|required|numeric',
            'price_per_day' => 'sometimes|required|numeric',
            'price_per_month' => 'sometimes|required|numeric',
             'state' => 'sometimes|string|max:100',
            'city' => 'sometimes|string|max:100',
            'street' => 'sometimes|string|max:255',
            'building_number' => 'sometimes|string|max:50',
            'level' => 'sometimes|integer',
            
            
            //TO Add New Photos
            'new_photos' => 'nullable|array',
            'new_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
            
            //TO Delete Existing Photos
            'delete_ids' => 'nullable|array', 
            'delete_ids.*' => 'integer|exists:apt_images,id', 
            'primary_id' => 'nullable|integer|exists:apt_images,id',
        ]);

        $apartment = Apartment::findOrFail($id);

        
        if ($apartment->owner_id !== Auth::id()) {
            return response()->json([
                'message' => 'Unauthorized action. You do not own this apartment.',
            ], 403);
        }

        
        $data = $request->except(['owner_id', 'is_available', 'new_photos', 'delete_ids', 'primary_id']);
        $apartment->update($data);

        
        
        // To Delete Existing Photos
       if ($request->filled('delete_ids')) {
        $imagesToDelete = $apartment->images()->whereIn('id', $request->delete_ids)->get();

        foreach ($imagesToDelete as $image) {
            
        
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            
            
            $image->delete(); 
        }
    }


        // To Add New Photos
        if ($request->hasFile('new_photos')) {
            $imagesToSave = [];
            foreach ($request->file('new_photos') as $photo) {
                $path = Storage::disk('public')->put('apartment_photos', $photo);

                $imagesToSave[] = new Apt_image(['image_path' => $path, 'is_primary' => false]);
            }
            $apartment->images()->saveMany($imagesToSave);
        }
        
        //To Set Primary Photo

        if ($request->filled('primary_id')) {
            $primaryId = $request->primary_id;
            
            $imageToSetPrimary = $apartment->images()->where('id', $primaryId)->first();

            if ($imageToSetPrimary) {
                $apartment->images()->update(['is_primary' => false]); 

                $imageToSetPrimary->update(['is_primary' => true]);
            }
        }

        return response()->json([
            'message' => 'The apartment and its images updated successfully',
            'apartment' => $apartment->load('images', 'owner'),
        ], 200);
    }

    // delete apartment
    /////////////////////////////////////////////////////////////////////////////////////////////////
  public function destroy($id)
{
    $apartment = Apartment::findOrFail($id);
    
    if ($apartment->owner_id !== Auth::id()) {
        return response()->json([
            'message' => 'Unauthorized action. You do not own this apartment.',
        ], 403);
    }
    
    $imagesToDelete = $apartment->images()->get(); 
    
    foreach ($imagesToDelete as $image) {
        
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }
        
        $image->delete(); 
    }
    
    
    $apartment->delete(); 

    return response()->json(['message' => 'The Apartment Deleted Successfully'], 200);
}
    // fillter
    /////////////////////////////////////////////////////////////////////////////////////////////////

    public function filter(Request $request)
    {
        $query = Apartment::where('is_available', true);

       if ($request->has('min_price_day')) {
        $query->where('price_per_day', '>=', $request->input('min_price_day'));
    }
    if ($request->has('max_price_day')) {
        $query->where('price_per_day', '<=', $request->input('max_price_day'));
    }
    
    if ($request->has('min_price_month')) {
        $query->where('price_per_month', '>=', $request->input('min_price_month'));
    }
    if ($request->has('max_price_month')) {
        $query->where('price_per_month', '<=', $request->input('max_price_month'));
    }

        if ($request->has('city')) {
            $query->where('city', $request->input('city'));
        }

        if ($request->has('state')) {
            $query->where('state', $request->input('state'));
        }

        $apartments = $query->with('images', 'owner')->get();

        return response()->json([$apartments, 200]);
    }
}