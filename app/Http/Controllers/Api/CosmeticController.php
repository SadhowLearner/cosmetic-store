<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryApiResource;
use App\Http\Resources\Api\CosmeticApiResource;
use App\Models\Cosmetic;
use Illuminate\Http\Request;

class CosmeticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cosmetics = Cosmetic::with(['brand', 'category']);

        if ($request->has('brand_id')) {
            $cosmetics->where('brand_id', $request->input('brand_id'));
        }
        if ($request->has('category_id')) {
            $cosmetics->where('category_id', $request->input('category_id'));
        }
        if ($request->has('limit')) {
            $cosmetics->limit($request->input('limit'));
        }
        if ($request->has('is_popular')) {
            $cosmetics->where('is_popular', $request->input('is_popular'));
        }

        return CosmeticApiResource::collection($cosmetics->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Cosmetic $cosmetic)
    {
        $cosmetic->load(['brand', 'category', 'benefits', 'photos', 'testimonials']);

        return new CategoryApiResource($cosmetic);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cosmetic $cosmetic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cosmetic $cosmetic)
    {
        //
    }
}
