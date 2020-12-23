<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Storage;
use App\Post;
use App\User;
use App\Following;

class UsersController extends Controller
{
    /**
     * ユーザー一覧表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!User::where('id', $request->user_id)->exists()) {
            \Session::flash('error', 'データがありません');
            return redirect(route('posts.index'));
        }
        $authuser_id = \Auth::id();
        $authuser_name = \Auth::user()->name;
        $target_user = User::where('id', $request->user_id)->first();

        // 現在のユーザーのフォロワー表示
        if($request->showfollowers) {
            $followings = Following::where('following_user_id', $request->user_id)->get();
            $users = [];
            foreach($followings as $following) {
                $user = User::where('id', $following->user_id)->get();
                array_push($users, $user);
            }
            return view('pages.users.index', [
                'users' => $users,
                'target_username' => $target_user->name,
                'target_userid' => $request->user_id,
                'authuser_id' => $authuser_id,
                'authuser_name' => $authuser_name,
                'showfollowers' => $request->showfollowers
            ]);
        } // 現在のユーザーのフォロー中表示
        else {
            $followings = Following::where('user_id', $request->user_id)->get();
            $users = [];
            foreach($followings as $following) {
                $user = User::where('id', $following->following_user_id)->get();
                array_push($users, $user);
            }
            return view('pages.users.index', [
                'users' => $users,
                'authuser_id' => $authuser_id,
                'target_username' => $target_user->name,
                'target_userid' => $request->user_id,
                'authuser_name' => $authuser_name,
                'showfollowers' => $request->showfollowers
            ]);
        }
    }

    /**
     * ユーザー詳細画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $user->load('posts.likes', 'posts.comments', 'followers', 'followings');
        $posts = Post::latest()->where('user_id', $user->id)->get();

        $authuser_id = \Auth::id();
        $authuser_name = \Auth::user()->name;

        // 現在のユーザーが対象ユーザーをフォローしているか判定
        $defaultFollowed = $user->followers->where('user_id', $authuser_id)->first();
        if(empty($defaultFollowed)) {
            $defaultFollowed = false;
        }
        else {
            $defaultFollowed = true;
        }
        $defaultfollowers_count = count($user->followers); // 対象ユーザーのフォロワー数
        $defaultfollowing_count = count($user->followings); // 対象ユーザーのフォロー中数
        $posts_count = count($posts); // 対象ユーザーの投稿数

        return view('pages.users.show', [
            'user' => $user,
            'authuser_id' => $authuser_id,
            'authuser_name' => $authuser_name,
            'defaultfollowers_count' => $defaultfollowers_count,
            'defaultfollowing_count' => $defaultfollowing_count,
            'posts_count' => $posts_count,
            'posts' => $posts,
            'defaultFollowed' => $defaultFollowed
        ]);
    }

    /**
     * プロフィール編集画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        // 不正アクセス防止
        if($user->id != \Auth::id()) {
            \Session::flash('error', '不正なアクセスです');
            return redirect(route('posts.index'));
        }

        return view('pages.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * プロフィール更新処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request)
    {
        // プロフィール画像が送信された時の処理
        if($request->hasFile('image')){
            if($request->file('image')->isValid()) {
                $user = \Auth::user();

                // S3から更新前の画像を削除
                $currentFilePath = $user->profileImg_url;
                if(isset($currentFilePath)){
                    $image = $user->pluck('profileImg_url');
                    $item = basename($image, '"]');
                    Storage::disk('s3')->delete('/laravel/profile_images/' . $item);
                }

                // S3に更新後の画像を保存
                $image = $request->file('image');
                $path = Storage::disk('s3')->put('/laravel/profile_images', $image, 'public');
                $profileImg_url = Storage::disk('s3')->url($path);
    
                $name = $request->input('name');
                $profile_content = $request->input('profile_content');
    
                // トランザクション処理
                \DB::beginTransaction();
                try {
                    $user = User::find($request->input('id'));
                    $user->fill([
                        'name' => $name,
                        'profile_content' => $profile_content,
                        'profileImg_url' => $profileImg_url
                    ]);
                    $user->save();
                    \DB::commit();
                } catch(\Throwable $e) {
                    \DB::rollback();
                    abort(500);
                }
            }
        } // プロフィール画像が送信されていない時の処理
        else {
            $name = $request->input('name');
            $profile_content = $request->input('profile_content');

            // トランザクション処理
            \DB::beginTransaction();
            try {
                $user = User::find($request->input('id'));
                $user->fill([
                    'name' => $name,
                    'profile_content' => $profile_content
                ]);
                $user->save();
                \Session::flash('status', 'プロフィールを更新しました');
                \DB::commit();
            } catch(\Throwable $e) {
                \DB::rollback();
                abort(500);
            }
        }
        return redirect(route('users.show', \Auth::id()));
    }
}
