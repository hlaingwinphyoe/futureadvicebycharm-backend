<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function getPosts(Request $request)
    {
        try {
            $page_size = $request->page_size ? $request->page_size : 12;
            $latestPostId = Post::latest('created_at')->first()->id;
            $posts = Post::query()
                ->with('user', 'category')
                ->where('id', '!=', $latestPostId)
                ->filterOn()
                ->published()
                ->orderBy('id', 'desc')
                ->paginate($page_size)
                ->withQueryString();

            $posts = PostResource::collection($posts)->response()->getData(true);

            return $this->sendResponse($posts, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getPost($slug)
    {
        try {
            $post = Post::with('user', 'category')
                ->where('slug', $slug)
                ->first();

            $post = new PostResource($post);
            return $this->sendResponse($post, "Success!");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getTodaySpecial()
    {
        try {
            $post = Post::with('user', 'category')
                ->orderBy('id', 'desc')
                ->first();

            $post = new PostResource($post);
            return $this->sendResponse($post, "Success!");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getRecentPost($id)
    {
        try {
            $posts = Post::query()
                ->with('user')
                ->filterOn()
                ->where('id', '!=', $id)
                ->published()
                ->inRandomOrder()
                ->get()
                ->take(5);

            $posts = PostResource::collection($posts);

            return $this->sendResponse($posts, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
