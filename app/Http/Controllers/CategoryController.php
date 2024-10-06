<?php

namespace App\Http\Controllers;

use App\Http\Resources\ArticleResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return CategoryResource::collection(
            Category::translatedIn(app()->getLocale())
                ->whereTranslation('in_menu', true)
                ->get()
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show($slug)
    {
        $category = Category::translatedIn(app()->getLocale())
            ->whereTranslation('slug', $slug)->firstOrFail();


        return new CategoryResource($category);
    }

    public function getCategoryArticles($slug){
        $category = Category::translatedIn(app()->getLocale())
            ->whereTranslation('slug', $slug)->firstOrFail();

        return ArticleResource::collection(
            $category->articles()
                ->translatedIn(app()->getLocale())
                ->paginate(12)
        );

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
