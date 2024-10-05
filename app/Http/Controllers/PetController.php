<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Storage;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener las mascotas del usuario autenticado
        $pets = Pet::where('user_id', Auth::id())->with('images', 'user')->get();

        return response()->json($pets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'age' => 'required|integer',
            'breed' => 'required|string',
            'size' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'adoption_status' => 'required|in:available,adopted,in_process',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Validar las imágenes
        ]);
    
        // Crear la mascota
        $pet = Pet::create([
            'name' => $validatedData['name'],
            'age' => $validatedData['age'],
            'breed' => $validatedData['breed'],
            'size' => $validatedData['size'],
            'description' => $validatedData['description'],
            'location' => $validatedData['location'],
            'adoption_status' => $validatedData['adoption_status'],
            'user_id' => auth()->id(),
        ]);
    
      
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $extension = $image->getClientOriginalExtension();
            $uniqueName = uniqid() . '_' . time() . '.' . $extension;
            $path = $image->storeAs('pets', $uniqueName, 'public');
            $savePath = Storage::url($path);
            $pet->images()->create(['urlImage' => $savePath]);
        }
    }
    
        return response()->json($pet->load('images'));
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Pet $pet)
    {
        // Verificar que la mascota pertenece al usuario autenticado
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Mostrar la mascota con sus imágenes
        return response()->json($pet->load('images'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pet $pet)
    {
        // Verificar que la mascota pertenece al usuario autenticado
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validación de los datos
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'age' => 'sometimes|required|integer|min:0',
            'breed' => 'sometimes|required|string|max:255',
            'size' => 'sometimes|required|string|max:50',
            'description' => 'sometimes|required|string',
            'location' => 'sometimes|required|string|max:255',
            'adoption_status' => 'sometimes|required|in:available,adopted,in_process',
            'images' => 'sometimes|required|array',
            'images.*' => 'sometimes|required|url',
        ]);

        // Actualizar la mascota
        $pet->update($request->only([
            'name', 'age', 'breed', 'size', 'description', 'location', 'adoption_status'
        ]));

        // Si se proporcionan nuevas imágenes, actualizarlas
        if ($request->has('images')) {
            // Eliminar las imágenes actuales
            $pet->images()->delete();

            // Guardar las nuevas imágenes
            foreach ($request->images as $imageUrl) {
                PetImage::create([
                    'pet_id' => $pet->id,
                    'urlImage' => $imageUrl,
                ]);
            }
        }

        return response()->json(['message' => 'Pet updated successfully!', 'pet' => $pet]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pet $pet)
    {
        // Verificar que la mascota pertenece al usuario autenticado
        if ($pet->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Eliminar la mascota y sus imágenes asociadas
        $pet->delete();

        return response()->json(['message' => 'Pet deleted successfully!']);
    }
}
