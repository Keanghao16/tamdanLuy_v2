<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        $categories = Category::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhereNull('user_id');
        })->get();
        // Group by 'group' or 'type' if 'group' is missing
        $groupedCategories = $categories->groupBy(function($item) {
            return $item->group ?: ucfirst($item->type);
        });
        
        return view('categories.index', compact('groupedCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        
        $validated = $request->validated();
        $validated['user_id'] = $userId;
        
        Category::create($validated);
        
        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;

        if ($category->is_default || $category->user_id !== $userId) {
            return redirect()->route('categories.index')->with('error', 'You cannot edit default categories.');
        }

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;

        if ($category->is_default || $category->user_id !== $userId) {
            return redirect()->route('categories.index')->with('error', 'You cannot edit default categories.');
        }

        $category->update($request->validated());

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
