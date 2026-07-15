<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use App\Models\BrandTranslation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BrandController extends \App\Http\Controllers\Controller
{
    public function index(): View
    {
        $brands = Brand::with('translation')->orderBy('sort_order')->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function create(): View
    {
        return view('admin.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|unique:brands,slug',
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'en.name' => 'required|string|max:255',
            'zh.name' => 'required|string|max:255',
        ]);

        $brand = Brand::create([
            'slug' => $request->slug,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->boolean('status', true),
        ]);

        foreach (['en', 'zh'] as $locale) {
            BrandTranslation::create([
                'brand_id' => $brand->id,
                'locale' => $locale,
                'name' => $request->input("$locale.name"),
                'description' => $request->input("$locale.description"),
            ]);
        }

        if ($request->hasFile('logo')) {
            $filename = $brand->slug . '-' . time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('uploads/brands'), $filename);
            $brand->update(['logo' => 'uploads/brands/' . $filename]);
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand): View
    {
        $brand->load('translations');
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|unique:brands,slug,' . $brand->id,
            'logo' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'en.name' => 'required|string|max:255',
            'zh.name' => 'required|string|max:255',
        ]);

        $brand->update([
            'slug' => $request->slug,
            'sort_order' => $request->sort_order ?? 0,
            'status' => $request->boolean('status', true),
        ]);

        foreach (['en', 'zh'] as $locale) {
            BrandTranslation::updateOrCreate(
                ['brand_id' => $brand->id, 'locale' => $locale],
                [
                    'name' => $request->input("$locale.name"),
                    'description' => $request->input("$locale.description"),
                ]
            );
        }

        if ($request->hasFile('logo')) {
            $oldLogo = $brand->logo;
            $filename = $brand->slug . '-' . time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('uploads/brands'), $filename);
            $brand->update(['logo' => 'uploads/brands/' . $filename]);
            // Delete old logo after successful move
            if ($oldLogo && file_exists(public_path($oldLogo))) {
                @unlink(public_path($oldLogo));
            }
        }

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->logo && file_exists(public_path($brand->logo))) {
            @unlink(public_path($brand->logo));
        }
        $brand->delete();
        return back()->with('success', 'Brand deleted.');
    }
}
