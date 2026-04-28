<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Http\Requests\PostRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    public function index()
    {
        try {
            $posts = Post::with('user')
                ->withCount(['likes', 'comments'])
                ->latest()
                ->get();

            // Гарантируем, что всегда возвращаем массив
            return response()->json($posts); 
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([], 500); // Возвращаем пустой массив при ошибке
        }
    }

    public function store(PostRequest $request)
    {
        $post = Post::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Post created successfully!',
            'post'    => $post->load('user')
        ], 201);
    }

    public function toggleLike(Post $post)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        $like = $post->likes()->where('user_id', $user->id)->first();

        if ($like) {
            $like->delete();
            return response()->json(['status' => false]);
        }

        $post->likes()->create(['user_id' => $user->id]);
        return response()->json(['status' => true]);
    }

    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $post->delete();
        return response()->json(['message' => 'Deleted']);
    }
}