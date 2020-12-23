<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use Storage;
use App\Post;
use App\Comment;
use App\Tag;

class PostsController extends Controller
{
    /**
     * 投稿一覧表示
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $q = \Request::query();
        // タグに該当する投稿一覧表示
        if(isset($q['tag_name'])) {
            $posts = Post::latest()->where('content', 'like', "%{$q['tag_name']}%")->get();
            $posts->load('user', 'tags', 'likes');
            return view('pages.posts.index', [
                'posts' => $posts,
                'tag_name' => $q['tag_name'],
                'index_page' => true
            ]);
        } // 通常の投稿一覧表示
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
     * 投稿作成画面表示
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.posts.create');
    }

    /**
     * 投稿保存処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PostRequest $request)
    {
        // 画像が送信された時の処理
        if($request->file('image')->isValid()) {
            $image = $request->file('image');

            // S3に保存
            $path = Storage::disk('s3')->put('/laravel/images', $image, 'public');
            $img_url = Storage::disk('s3')->url($path);

            $title = $request->input('title');
            $content = $request->input('content');

            // 正規表現でハッシュタグのついたワードをタグに設定する
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

            // トランザクション処理
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
        return redirect(route('posts.index')); // 投稿一覧画面にリダイレクト
    }

    /**
     * 投稿詳細画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        $post->load('user', 'likes', 'comments.user');
        $user = \Auth::user();
        $defaultCount = count($post->likes); // 投稿のいいね数
        $defaultLiked = $post->likes->where('user_id', $user->id)->first(); // 投稿に対して現在のユーザーのいいね

        // 投稿に対して現在のユーザーのいいね判定
        if(empty($defaultLiked)) {
            $defaultLiked = false;
        }
        else {
            $defaultLiked = true;
        }

        // 投稿に対するコメント
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
     * 投稿編集画面表示
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        // 不正アクセス防止
        if($post->user != \Auth::user()) {
            \Session::flash('error', '不正なアクセスです');
            return redirect(route('posts.index'));
        }

        return view('pages.posts.edit', [
            'post' => $post
        ]);
    }

    /**
     * 投稿更新処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PostRequest $request, Post $post)
    {
        // 画像が送信された時の処理
        if($request->file('image')->isValid()) {
            $currentFilePath = $post->img_url;

            // S3から更新前の画像を削除
            if(isset($currentFilePath)){
                $image = $post->pluck('img_url');
                $item = basename($image, '"]');
                Storage::disk('s3')->delete('/laravel/images/' . $item);
            }

            // S3に更新後の画像を保存
            $image = $request->file('image');
            $path = Storage::disk('s3')->put('/laravel/images', $image, 'public');
            $img_url = Storage::disk('s3')->url($path);

            $user_id = $post->user->id;
            $title = $request->input('title');
            $content = $request->input('content');

            // 正規表現でハッシュタグのついたワードをタグに設定する
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

            // トランザクション処理
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
     * 投稿削除処理
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        // 投稿が既に存在しない場合
        if(empty($post->id)) {
            \Session::flash('error', 'データがありません');
            return redirect(route('posts.index'));
        }// 投稿者と現在のユーザーが異なる場合
        elseif($post->user != \Auth::user()) {
            \Session::flash('error', '不正なアクセスです');
            return redirect(route('posts.index'));
        }    
        try {
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

    /**
     * 投稿検索処理
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        // 部分一致で投稿検索
        $posts = Post::latest()->where('title', 'like', "%{$request->search}%")->orWhere('content', 'like', "%{$request->search}%")->get();
        $search_request = $request->search;

        return view('pages.posts.index', [
            'posts' => $posts,
            'search_request' => $search_request,
            'index_page' => true
        ]);
    }
}
