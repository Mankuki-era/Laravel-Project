<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Following;

class FollowingsController extends Controller
{
    public function follow(User $user, Request $request) {
        Following::create([
            'user_id' => $request->authuserId,
            'following_user_id' => $user->id
        ]);
        $followers_count = count($user->followers);
        $following_count = count($user->followings);
        $followed = true;

        return response()->json([
            'followers_count' => $followers_count,
            'following_count' => $following_count,
            'followed' => $followed
        ]);
    }

    public function unfollow(User $user, Request $request) {
        $follow = Following::where('user_id', $request->authuserId)->where('following_user_id', $user->id)->first();
        $follow->delete();
        $followers_count = count($user->followers);
        $following_count = count($user->followings);
        $followed = false;

        return response()->json([
            'followers_count' => $followers_count,
            'following_count' => $following_count,
            'followed' => $followed
        ]);
    }
}
