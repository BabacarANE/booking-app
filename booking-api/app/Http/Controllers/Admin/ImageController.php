<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Models\ResourceImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    // ✅ Upload plusieurs images
    public function upload(Request $request, Resource $resource)
    {
        $request->validate([
            'images'   => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $index => $file) {
            $path = $file->store("resources/{$resource->id}", 'public');
            $url  = Storage::url($path);

            $isPrimary = $index === 0 && $resource->images_list()->count() === 0;

            $image = ResourceImage::create([
                'resource_id' => $resource->id,
                'path'        => $path,
                'url'         => $url,
                'is_primary'  => $isPrimary,
                'order'       => $resource->images_list()->count() + $index,
            ]);

            $uploaded[] = $image;
        }

        return response()->json([
            'message' => count($uploaded) . ' image(s) uploadée(s)',
            'images'  => $uploaded,
        ], 201);
    }

    // ✅ Supprimer une image
    public function destroy(ResourceImage $image)
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();

        return response()->json(['message' => 'Image supprimée.']);
    }

    // ✅ Définir image principale
    public function setPrimary(ResourceImage $image)
    {
        // Reset toutes les images de la ressource
        ResourceImage::where('resource_id', $image->resource_id)
            ->update(['is_primary' => false]);

        $image->update(['is_primary' => true]);

        return response()->json(['message' => 'Image principale définie.']);
    }
}
