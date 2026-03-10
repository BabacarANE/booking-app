<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index()
    {
        // ✅ Cache 1 heure — les catégories changent rarement
        $categories = Cache::remember('categories', 3600, function () {
            return Category::withCount('resources')->get();
        });

        return CategoryResource::collection($categories);
    }
}
