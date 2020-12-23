<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Like;

class LikesController extends Controller
{
    /**
     * いいね処理
     *
     * @param  \App\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function like(Post $post, Request $request) {
        // 対象投稿をいいね
        $like = Like::create([
            'user_id' => $request->user_id,
            'post_id' => $post->id
        ]);
        $likeCount = count(Like::where('post_id', $post->id)->get()); // 対象投稿のいいね数
        $liked = true;

        // json形式で返却
        return response()->json([
            'likeCount' => $likeCount,
            'liked' => $liked
        ]);
    }

    /**
     * いいね解除処理
     *
     * @param  \App\Post  $post
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function unlike(Post $post, Request $request) {
        $like = Like::where('user_id', $request->user_id)->where('post_id', $post->id)->first();
        $like->delete(); // 対象投稿へのいいね解除
        $likeCount = count(Like::where('post_id', $post->id)->get()); // 対象投稿のいいね数
        $liked = false;

        // json形式で返却
        return response()->json([
            'likeCount' => $likeCount,
            'liked' => $liked
        ]);
    }
}
