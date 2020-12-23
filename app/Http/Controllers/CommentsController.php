<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Comment;

class CommentsController extends Controller
{
    /**
     * コメント保存処理
     *
     * @param  \App\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function store(Post $post, Request $request) {
        // コメント保存
        $comment = Comment::create([
            'user_id' => $request->user_id,
            'post_id' => $post->id,
            'content' => $request->content
        ]);
        $created_at = $comment->created_at->format('Y/m/d H:i:s'); // コメント投稿時刻

        // json形式で返却
        return response()->json([
            'created_at' => $created_at,
            'user_name' => $request->user_name,
            'profileImg_url' => $request->profileImg_url,
            'content' => $request->content
        ]);
    }
}
