<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use App\Http\Resources\ResourceResource;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::with(['category', 'images_list'])->latest()->paginate(20);
        return ResourceResource::collection($resources);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'description'     => 'nullable|string',
            'price_per_night' => 'required|numeric|min:0',
            'capacity'        => 'required|integer|min:1',
            'amenities'       => 'nullable|array',
            'location'        => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();

        $resource = Resource::create($validated);

        return new ResourceResource($resource->load('category'));
    }

    public function show(Resource $resource)
    {
        return new ResourceResource($resource->load('category'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'category_id'     => 'sometimes|exists:categories,id',
            'description'     => 'nullable|string',
            'price_per_night' => 'sometimes|numeric|min:0',
            'capacity'        => 'sometimes|integer|min:1',
            'amenities'       => 'nullable|array',
            'location'        => 'nullable|string',
            'is_active'       => 'boolean',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . uniqid();
        }

        $resource->update($validated);

        return new ResourceResource($resource->load('category'));
    }

    public function destroy(Resource $resource)
    {
        $resource->update(['is_active' => false]);

        return response()->json([
            'message' => 'Ressource désactivée avec succès.',
        ]);
    }
}
