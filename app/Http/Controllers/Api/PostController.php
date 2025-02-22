<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostView;
use App\Models\UpvoteDownVote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function getPosts(Request $request)
    {
        try {
            $page_size = $request->page_size ? $request->page_size : 12;
            $latestPostId = Post::orderBy('id', 'desc')->first()->id;
            $posts = Post::query()
                ->with('user', 'category', 'tags')
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

    public function getPost(Request $request, $slug)
    {
        try {
            $post = Post::with('user', 'category', 'tags', 'votes', 'post_views')
                ->where('slug', $slug)
                ->first();

            if (!$post) {
                return $this->sendError("Post not found", 404);
            }
            $user = User::where('id', $request->userId)->first();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Check if the user has already viewed this post in the last hour
            $viewExists = PostView::where('post_id', $post->id)
                ->where(function ($query) use ($user, $ipAddress, $userAgent) {
                    // Check by user ID (if logged in)
                    if ($user) {
                        $query->where('user_id', $user->id);
                    } else {
                        // Check by IP address and user agent (for guests)
                        $query->where('ip_address', $ipAddress)
                            ->where('user_agent', $userAgent);
                    }
                })
                ->where('created_at', '>=', now()->subHour()) // Views within the last hour
                ->exists();

            // Only create a new view record if none exists in the last hour
            if (!$viewExists) {
                PostView::create([
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'post_id' => $post->id,
                    'user_id' => $user ? $user->id : null
                ]);
            }

            // Return the post with the necessary data
            $postResource = new PostResource($post);
            return $this->sendResponse($postResource, "Success!");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }


    public function getTodaySpecial()
    {
        try {
            $post = Post::with('user', 'category', 'tags')
                ->published()
                ->orderBy('id', 'desc')
                ->limit(1)
                ->first();

            $post = new PostResource($post);
            return $this->sendResponse($post, "Success!");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getPopularPosts()
    {
        try {
            $latestPostId = Post::orderBy('id', 'desc')->first()->id;
            $posts = Post::query()
                ->with('user', 'category', 'tags')
                ->leftJoin('upvote_down_votes', 'posts.id', '=', 'upvote_down_votes.post_id')
                ->select('posts.*', DB::raw('count(upvote_down_votes.id) as upvote_count'))
                ->where('posts.id', '!=', $latestPostId)
                ->published()
                ->orderByDesc('upvote_count')
                ->groupBy('posts.id')
                ->limit(2)
                ->get();

            $popularPosts = PostResource::collection($posts);

            return $this->sendResponse($popularPosts, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getMostViewPosts()
    {
        try {
            $mostViewPosts = Post::query()
                ->with('user', 'category', 'tags')
                ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
                ->select('posts.*', DB::raw('count(post_views.id) as view_count'))
                ->published()
                ->orderByDesc('view_count')
                ->groupBy('posts.id')
                ->limit(5)
                ->get();

            $posts = PostResource::collection($mostViewPosts);

            return $this->sendResponse($posts, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function getRecommendedPosts($id)
    {
        try {
            $posts = Post::query()
                ->with('user', 'category', 'tags')
                ->leftJoin('post_views', 'posts.id', '=', 'post_views.post_id')
                ->select('posts.*', DB::raw('count(post_views.id) as view_count'))
                ->published()
                ->where('posts.id', '!=', $id)
                ->orderByDesc('view_count')
                ->groupBy('posts.id')
                ->limit(5)
                ->get();
            // $posts = Post::query()
            //     ->with('user', 'category', 'tags')
            //     ->filterOn()
            //     ->where('id', '!=', $id)
            //     ->published()
            //     ->orderBy('id', 'desc')
            //     ->get()
            //     ->take(5);

            $recommendedPosts = PostResource::collection($posts);

            return $this->sendResponse($recommendedPosts, 'Success!');
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function upVoteDownVote($id)
    {
        $post = Post::findOrFail($id);
        $upvotes = UpvoteDownVote::where('post_id', $post->id)
            ->where('is_upvote', true)
            ->get();

        // $downvotes = UpvoteDownVote::where('post_id', $post->id)
        //     ->where('is_upvote', false)
        //     ->count();

        return $this->sendResponse($upvotes, 'Success!');
    }

    public function upVoteDownVoteStore(Request $request, $id)
    {
        $user = $request->user();

        if (!$user) {
            return $this->sendError('Unauthorized', 401);
        }

        // if (!$user->hasVerifiedEmail()) {
        //     return $this->sendError('Please verify your email', 401);
        // }

        DB::transaction(function () use ($user, $id) {
            $post = Post::with('user', 'category', 'tags', 'votes')->findOrFail($id);

            $modal = UpvoteDownVote::where('post_id', $post->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$modal) {
                UpvoteDownVote::create([
                    'is_upvote' => true,
                    'post_id' => $post->id,
                    'user_id' => $user->id
                ]);
            } else {
                $modal->delete();
            }

            return $this->sendResponse($modal, "Success!");
        });
    }
}
