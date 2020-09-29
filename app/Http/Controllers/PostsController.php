<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
// use Illuminate\Support\Facades\Storage;
use Storage;
use App\Post;
use App\Comment;
use App\Tag;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = \Request::query();
        
        if(isset($q['tag_name'])) {
            $posts = Post::latest()->where('content', 'like', "%{$q['tag_name']}%")->get();
            $posts->load('user', 'tags', 'likes');
            return view('pages.posts.index', [
                'posts' => $posts,
                'tag_name' => $q['tag_name'],
                'index_page' => true
            ]);
        }
        else {
            $posts = Post::latest()->get();
            $posts->load('user', 'tags', 'likes');
            return view('pages.posts.index', [
                'posts' => $posts,
                'index_page' => true
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
        return view('pages.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        if($request->file('image')->isValid()) {
            // $filename = $request->file('image')->getClientOriginalName();
            // $request->file('image')->storeAs('public/images', $filename);
            // $fullPath = '/storage/images/' . $filename;

            $image = $request->file('image');
            $path = Storage::disk('s3')->put('/laravel/images', $image, 'public');
            $img_url = Storage::disk('s3')->url($path);

            $title = $request->input('title');
            $content = $request->input('content');

            preg_match_all('/#([a-zA-Z0-9０-９ぁ-んァ-ヶー一-龠]+)/u', $content, $match);
            $tags = [];
            foreach ($match[1] as $tag) {
                $found = Tag::firstOrCreate([
                    'tag_name' => $tag
                ]);
                array_push($tags, $found);
            }
            $tag_ids = [];
            foreach($tags as $tag) {
                array_push($tag_ids, $tag['id']);
            }

            \DB::beginTransaction();
            try {
                $post = Post::create([
                    'title' => $title,
                    'content' => $content,
                    'img_url' => $img_url,
                    'user_id' => \Auth::id()
                ]);
                $post->tags()->attach($tag_ids);
                \Session::flash('status', '投稿が完了しました');
                \DB::commit();
            }catch(\Throwable $e) {
                \DB::rollback();
                abort(500);
            }
        }
        return redirect(route('posts.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post->load('user', 'likes', 'comments.user');
        $user = \Auth::user();
        $defaultCount = count($post->likes);
        $defaultLiked = $post->likes->where('user_id', $user->id)->first();
        if(empty($defaultLiked)) {
            $defaultLiked = false;
        }
        else {
            $defaultLiked = true;
        }
        $comments = Comment::where('post_id', $post->id)->get();
        $comments_array = [];
        foreach($comments as $comment) {
            array_push($comments_array, [
                'userName' => $comment->user->name,
                'profileImgUrl' => $comment->user->profileImg_url,
                'content' => $comment->content,
                'createdAt' => $comment->created_at->format('Y/m/d H:i:s')
            ]);
        };
        return view('pages.posts.show', [
            'post' => $post,
            'user' => $user,
            'comments_array' => $comments_array,
            'defaultCount' => $defaultCount,
            'defaultLiked' => $defaultLiked,
            'index_page' => false
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        if($post->user != \Auth::user()) {
            \Session::flash('error', '不正なアクセスです');
            return redirect(route('posts.index'));
        }

        return view('pages.posts.edit', [
            'post' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        if($request->file('image')->isValid()) {
            // $currentFilePath = $post->img_url;
            // if(isset($currentFilePath)){
            //     $currentFileName = str_replace('/storage/images/', '', $currentFilePath);
            //     Storage::delete('public/images/' . $currentFileName);
            // }
            // $filename = $request->file('image')->getClientOriginalName();
            // $request->file('image')->storeAs('public/images', $filename);
            // $fullFilePath = '/storage/images/' . $filename;

            $currentFilePath = $post->img_url;
            if(isset($currentFilePath)){
                $image = $post->pluck('img_url');
                $item = basename($image, '"]');
                Storage::disk('s3')->delete('/laravel/images/' . $item);
            }
            $image = $request->file('image');
            $path = Storage::disk('s3')->put('/laravel/images', $image, 'public');
            $img_url = Storage::disk('s3')->url($path);

            $user_id = $post->user->id;
            $title = $request->input('title');
            $content = $request->input('content');

            preg_match_all('/#([a-zA-Z0-9０-９ぁ-んァ-ヶー一-龠]+)/u', $content, $match);
            $tags = [];
            foreach ($match[1] as $tag) {
                $found = Tag::firstOrCreate([
                    'tag_name' => $tag
                ]);
                array_push($tags, $found);
            }
            $tag_ids = [];
            foreach($tags as $tag) {
                array_push($tag_ids, $tag['id']);
            }

            \DB::beginTransaction();
            try {
                $post->fill([
                    'user_id' => $user_id,
                    'title' => $title,
                    'content' => $content,
                    'img_url' => $img_url
                ]);
                $post->save();
                $post->tags()->attach($tag_ids);
                \Session::flash('status', '投稿を更新しました');
                \DB::commit();
            } catch(\Throwable $e) {
                \DB::rollback();
                abort(500);
            }
        }

        return redirect(route('posts.show', $post->id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        if(empty($post->id)) {
            \Session::flash('error', 'データがありません');
            return redirect(route('posts.index'));
        }
        elseif($post->user != \Auth::user()) {
            \Session::flash('error', '不正なアクセスです');
            return redirect(route('posts.index'));
        }

        // $currentFilePath = $post->img_url;
        // $currentFileName = str_replace('/storage/images/', '', $currentFilePath);
        
        try {
            // Storage::delete('public/images/' . $currentFileName);
            $image = $post->pluck('img_url');
            $item = basename($image, '"]');
            Storage::disk('s3')->delete('/laravel/images/' . $item);
            $post->delete();
        } catch(\Throwable $e) {
            abort(500);
        }

        \Session::flash('status', '投稿を削除しました');
        return redirect(route('posts.index'));
    }

    public function search(Request $request) {
        $posts = Post::latest()->where('title', 'like', "%{$request->search}%")->orWhere('content', 'like', "%{$request->search}%")->get();
        $search_request = $request->search;

        return view('pages.posts.index', [
            'posts' => $posts,
            'search_request' => $search_request,
            'index_page' => true
        ]);
    }
}
