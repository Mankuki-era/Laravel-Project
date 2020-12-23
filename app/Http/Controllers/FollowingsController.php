<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Following;

class FollowingsController extends Controller
{
    /**
     * フォロー処理
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function follow(User $user, Request $request) {
        // 対象ユーザーをフォロー
        Following::create([
            'user_id' => $request->authuserId,
            'following_user_id' => $user->id
        ]);
        $followers_count = count($user->followers); // 対象ユーザーのフォロワー数
        $following_count = count($user->followings); // 対象ユーザーのフォロー中数
        $followed = true;

        // json形式で返却
        return response()->json([
            'followers_count' => $followers_count,
            'following_count' => $following_count,
            'followed' => $followed
        ]);
    }

    /**
     * フォロー解除処理
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return json
     */
    public function unfollow(User $user, Request $request) {
        $follow = Following::where('user_id', $request->authuserId)->where('following_user_id', $user->id)->first();
        $follow->delete(); // 対象ユーザーへのフォロー解除
        $followers_count = count($user->followers); // 対象ユーザーのフォロワー数
        $following_count = count($user->followings); // 対象ユーザーのフォロー中数
        $followed = false;

        // json形式で返却
        return response()->json([
            'followers_count' => $followers_count,
            'following_count' => $following_count,
            'followed' => $followed
        ]);
    }
}
