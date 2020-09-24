<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Comment;

class CommentsController extends Controller
{
    public function store(Post $post, Request $request) {

        $comment = Comment::create([
            'user_id' => $request->user_id,
            'post_id' => $post->id,
            'content' => $request->content
        ]);

        $created_at = $comment->created_at->format('Y/m/d H:i:s');

        return response()->json([
            'created_at' => $created_at,
            'user_name' => $request->user_name,
            'profileImg_url' => $request->profileImg_url,
            'content' => $request->content
        ]);
    }
}
