<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $categories = Category::query()
            ->filterOn()
            ->latest()
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'image' => $category->image,
                'created_at' => $category->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Blog/Category/Index', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        DB::transaction(function () use ($request) {
            Category::create([
                'name' => $request->name,
            ]);
        });

        return redirect()->back()->with('success', 'Successfully Created.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        DB::transaction(function () use ($request, $category) {
            $category->update([
                'name' => $request->name,
            ]);
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->posts()->count() > 0) {
            return redirect()->back()->with('error', 'Category already link with posts.');
        } else {
            DB::transaction(function () use ($category) {
                $category->delete();
            });

            return redirect()->back()->with('success', 'Successfully Deleted.');
        }
    }
}
