<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Services\MediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{
    private $mediaSvc;
    public function __construct(MediaService $mediaSvc)
    {
        $this->mediaSvc = $mediaSvc;
    }

    public function index()
    {
        $pageSize = request('page_size') ?: 10;
        $page = intval(request('page')) ?: 1;
        $posts = Post::query()
            ->with(['category', 'user'])
            ->filterOn()
            ->orderBy('id', 'desc')
            ->paginate($pageSize)
            ->withQueryString()
            ->through(fn($post) => [
                'id' => $post->id,
                'title' => $post->title,
                'cate_name' => $post->category ? $post->category->name : '',
                'category_id' => $post->category_id,
                'excerpt' => $post->excerpt,
                'desc' => $post->desc,
                'is_published' => $post->is_published,
                'poster' => $post->media,
                'owner' => $post->owner,
                'read_time' => $post->read_time,
                'created_at' => $post->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Blog/Post/Index', [
            'posts' => $posts,
            'page' => $page,
        ]);
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return Inertia::render('Admin/Blog/Post/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'category' => 'required|numeric|exists:categories,id',
            'desc' => 'required|string|min:10',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $post = Post::create([
                'title' => $request->title,
                'desc' => $request->desc,
                'category_id' => $request->category,
                'excerpt' => Str::words($request->desc, 50),
                'user_id' => Auth::id(),
            ]);

            if ($request->hasFile('poster')) {
                $mediaFormdata = [
                    'media' => $request->file('poster'),
                    'type' => "poster",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $post->update([
                    'poster' => $url
                ]);
            }

            DB::commit();

            return redirect()->route('admin.posts.index')->with('success', 'Successfully Created.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return Inertia::render('Admin/Blog/Post/Edit', [
            'post' => $post,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|string|min:3',
            'category' => 'required|numeric|exists:categories,id',
            'desc' => 'required|string|min:10',
            'poster' => 'nullable'
        ]);

        try {
            DB::beginTransaction();

            $post->update([
                'title' => $request->title,
                'desc' => $request->desc,
                'category_id' => $request->category,
                'excerpt' => Str::words($request->desc, 50),
                'user_id' => Auth::id(),
            ]);

            if ($request->hasFile('poster')) {
                // Delete the old image
                if ($post->poster !== null) {
                    Storage::disk('public')->delete($post->poster);
                }

                $mediaFormdata = [
                    'media' => $request->file('poster'),
                    'type' => "poster",
                ];

                $url = $this->mediaSvc->storeMedia($mediaFormdata);

                $post->update([
                    'poster' => $url
                ]);
            }

            DB::commit();

            return redirect()->route('admin.posts.index')->with('success', 'Successfully Updated.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function changeStatus(Post $post)
    {
        $data = DB::transaction(function () use ($post) {
            if ($post->is_published === 0) {
                $post->update(['is_published' => 1]);
            } else {
                $post->update(['is_published' => 0]);
            }
        });

        return redirect()->back()->with('success', 'Successfully Updated.');
    }

    public function destroy(Post $post)
    {
        $data = DB::transaction(function () use ($post) {
            $post->delete();
        });

        return redirect()->back()->with('success', 'Successfully Deleted.');
    }
}
