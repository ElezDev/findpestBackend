<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index()
    {
        // Ordenar los registros por 'created_at' en orden descendente
        $pets = Pet::with(['images', 'user'])->orderBy('created_at', 'desc')->get();
    
        return response()->json($pets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $pet = Pet::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'age' => $request->age,
            'breed' => $request->breed,
            'size' => $request->size,
            'description' => $request->description,
            'location' => $request->location,
            'adoption_status' => $request->adoption_status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    
        // Manejo de imágenes
        if ($request->has('images')) {
            PetImageController::store(new Request(['images' => $request->images, 'pet_id' => $pet->id]));
        }
    
        $pet->load('images');
    
        return response()->json($pet, 201);
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'age' => 'required|integer|min:0',
            'breed' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'adoption_status' => 'required|in:available,adopted,in_process',
            'latitude' => 'required|string|max:255',
            'longitude' =>'required|string|max:255',
        ]);

        $pet->update([
            'name' => $request->name,
            'age' => $request->age,
            'breed' => $request->breed,
            'size' => $request->size,
            'description' => $request->description,
            'location' => $request->location,
            'adoption_status' => $request->adoption_status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json($pet, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        foreach ($pet->images as $image) {
            $image->delete(); 
        }
        $pet->delete(); 

        return response()->json(['message' => 'Pet deleted successfully!']);
    }


    public function getPetsByUser()
    {
        $pets = Pet::where('user_id', Auth::id())->with('images', 'user')->get();

        // return response()->json($pets);

        // $pets = Pet::all()->load('images');
        return response()->json($pets);

    }
}
