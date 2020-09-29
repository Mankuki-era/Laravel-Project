<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
// use Illuminate\Support\Facades\Storage;
use Storage;
use App\Post;
use App\User;
use App\Following;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
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
        }
        else {
            $followings = Following::where('user_id', $request->user_id)->get();
            $users = [];
            foreach($followings as $following) {
                $user = User::where('id', $following->user_id)->get();
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
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
        $defaultFollowed = $user->followers->where('user_id', $authuser_id)->first();
        if(empty($defaultFollowed)) {
            $defaultFollowed = false;
        }
        else {
            $defaultFollowed = true;
        }
        $defaultfollowers_count = count($user->followers);
        $defaultfollowing_count = count($user->followings);
        $posts_count = count($posts);

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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        if($user->id != \Auth::id()) {
            \Session::flash('error', '不正なアクセスです');
            return redirect(route('posts.index'));
        }

        return view('pages.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request)
    {
        if($request->hasFile('image')){
            if($request->file('image')->isValid()) {
                // $user = \Auth::user();
                // $currentFilePath = $user->profileImg_url;
                // if(isset($currentFilePath)){
                //     $currentFileName = str_replace('/storage/profile_images/', '', $currentFilePath);
                //     Storage::delete('public/profile_images/' . $currentFileName);
                // }
                // $filename = $request->file('image')->getClientOriginalName();
                // $request->file('image')->storeAs('public/profile_images', $filename);
                // $fullFilePath = '/storage/profile_images/' . $filename;

                $user = \Auth::user();
                $currentFilePath = $user->profileImg_url;
                if(isset($currentFilePath)){
                    $image = $user->pluck('profileImg_url');
                    $item = basename($image, '"]');
                    Storage::disk('s3')->delete('/laravel/profile_images/' . $item);
                }
                $image = $request->file('image');
                $path = Storage::disk('s3')->put('/laravel/profile_images', $image, 'public');
                $profileImg_url = Storage::disk('s3')->url($path);
    
                $name = $request->input('name');
                $profile_content = $request->input('profile_content');
    
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
        }
        else {
            $name = $request->input('name');
            $profile_content = $request->input('profile_content');

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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
