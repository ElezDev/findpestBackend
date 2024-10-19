<?php

namespace App\Http\Controllers;

use App\Models\PetImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PetImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $imagesPets = PetImage::where('pet_id', $request->pet_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json($imagesPets);
    }

    /**
     * Store a newly created resource in storage.
     */
    public static function store(Request $request): JsonResponse
    {
        // $request->validate([
        //     'pet_id' => 'required|integer',
        //     'images' => 'required|array',
        //     'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);

        $imagesData = [];
        foreach ($request->images as $image) {
            $rutaAlmacenamiento = $image->store('images/pet_images', 'public');
            $rutaImagenGuardada = Storage::url($rutaAlmacenamiento);

            $petImage = PetImage::create([
                'pet_id'    => $request->pet_id,
                'image_url' => $rutaImagenGuardada,
            ]);

            $imagesData[] = $petImage;
        }

        return response()->json($imagesData, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $petImage = PetImage::findOrFail($id);
        return response()->json($petImage);
    }

    /**
     * Update the specified resource in storage.
     */
    public static function update(Request $request, string $id): JsonResponse
    {
        // $request->validate([
        //     'pet_id' => 'required|integer',
        //     'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        // ]);

        $petImage = PetImage::findOrFail($id);

        $petImage->pet_id = $request->pet_id;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            self::deleteImage($petImage);
            $rutaAlmacenamiento = $request->file('image')->store('images/pet_images', 'public');
            $rutaImagenGuardada = Storage::url($rutaAlmacenamiento);

            $petImage->image_url = $rutaImagenGuardada;
        }

        $petImage->save();

        return response()->json($petImage, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $petImage = PetImage::findOrFail($id);
        $this->deleteImage($petImage);
        $petImage->delete();
        return response()->json(null, 204);
    }

    /**
     * Delete image of Team
     *
     * @param object $pago
     * @return void
     */
    private static function deleteImage($file): void
    {
        if ($file->image_url) {
            $urlImage = str_replace(url('storage/'), '', $file->image_url);
            Storage::disk('public')->delete($urlImage);
        }
    }
}
