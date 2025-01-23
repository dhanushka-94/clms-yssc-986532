<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::latest()->paginate(10);
        $types = Category::getTypes();
        
        return view('categories.index', compact('categories', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $types = Category::getTypes();
        return view('categories.create', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(Category::getTypes()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7'], // hex color code
            'is_active' => ['boolean'],
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
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
    public function edit(Category $category): View
    {
        $types = Category::getTypes();
        return view('categories.edit', compact('category', 'types'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:' . implode(',', array_keys(Category::getTypes()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:7'],
            'is_active' => ['boolean'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category is in use
        if ($category->sponsors()->exists() || $category->financialTransactions()->exists()) {
            return back()->with('error', 'Cannot delete category as it is being used.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
