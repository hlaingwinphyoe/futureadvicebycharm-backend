<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ZodiacResource;
use App\Models\Post;
use App\Models\Zodiac;
use Illuminate\Http\Request;

class ApiFrontController extends Controller
{
    public function getPosts(Request $request)
    {
        try {
            $page_size = $request->page_size ? $request->page_size : 12;
            $posts = Post::query()
                ->with('user', 'category')
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
                ->filterOn()
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

    public function getZodiacs()
    {
        $zodiacs = Zodiac::all();

        $zodiacs = ZodiacResource::collection($zodiacs);

        return $this->sendResponse($zodiacs, 'Success!');
    }
}
