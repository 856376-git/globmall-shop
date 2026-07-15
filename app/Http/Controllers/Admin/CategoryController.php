<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\CategoryTranslation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CategoryController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        $categories = Category::with(['translation', 'children.translation'])
            ->topLevel()
            ->orderBy('sort_order')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parentCategories = Category::with('translation')->topLevel()->get();
        return view('admin.categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'en.name' => 'required|string|max:255',
            'zh.name' => 'required|string|max:255',
        ]);

        $category = Category::create([
            'slug' => $request->slug,
            'parent_id' => $request->parent_id ?: null,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->boolean('status', true),
        ]);

        foreach (['en', 'zh'] as $locale) {
            CategoryTranslation::create([
                'category_id' => $category->id,
                'locale' => $locale,
                'name' => $request->input("$locale.name"),
                'description' => $request->input("$locale.description"),
                'meta_title' => $request->input("$locale.meta_title"),
                'meta_description' => $request->input("$locale.meta_description"),
            ]);
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        $category->load('translations');
        $parentCategories = Category::with('translation')->topLevel()->where('id', '!=', $category->id)->get();
        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'en.name' => 'required|string|max:255',
            'zh.name' => 'required|string|max:255',
        ]);

        $category->update([
            'slug' => $request->slug,
            'parent_id' => $request->parent_id ?: null,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->boolean('status', true),
        ]);

        foreach (['en', 'zh'] as $locale) {
            CategoryTranslation::updateOrCreate(
                ['category_id' => $category->id, 'locale' => $locale],
                [
                    'name' => $request->input("$locale.name"),
                    'description' => $request->input("$locale.description"),
                    'meta_title' => $request->input("$locale.meta_title"),
                    'meta_description' => $request->input("$locale.meta_description"),
                ]
            );
        }

        return redirect()->route('admin.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Reassign children to parent or set null before deleting
        if ($category->children()->count() > 0) {
            $category->children()->update(['parent_id' => $category->parent_id]);
        }
        $category->delete();
        return back()->with('success', 'Category deleted. Sub-categories moved to parent.');
    }
}
