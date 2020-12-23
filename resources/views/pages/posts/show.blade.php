@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
      投稿詳細画面
  </h1>
</div>
@if (session('status'))
  <div class="alert alert-success session-msg" role="alert">
      {{ session('status') }}
  </div>
@endif
<div class="showPage">
  <div class="card-box">
    <div class="card-item">
    <?php 
      // 本文からタグ部分を削除する
      $newContent = '';
      if(isset($post->tags)) {
        $target = [];
        foreach($post->tags as $tag) {
          array_push($target, '#' . $tag->tag_name);
        }
        $newContent = str_replace($target, '', $post->content);
      }
    ?>
      <div class="user">
        <a href="{{ route('users.show', $post->user->id) }}" class="auth-name">
          <img src="{{ $post->user->profileImg_url }}" alt="プロフィール画像"><span>{{ $post->user->name }}</span>
        </a>
      </div>
      <div class="image">
        <img src="{{ $post->img_url }}" alt="画像">
      </div>
      <div class="icons">
        <div class="left-icons">
          <div class="heart">
            <!-- いいね機能 -->
            <like-component
            :post-id="{{ json_encode($post->id) }}"
            :user-id="{{ json_encode($user->id) }}"
            :default-Count="{{ json_encode($defaultCount) }}"
            :default-Liked="{{ json_encode($defaultLiked) }}"
            :index-page="{{ json_encode($index_page) }}"
            ></like-component>
          </div>
          <!-- 投稿ユーザーと現在のユーザーが一緒の場合 -->
          @if($post->user == \Auth::user())
          <div class="edit">
            <a href="{{ route('posts.edit', $post->id) }}" class="edit-link">
              <i class="far fa-edit fa-lg edit-icon"></i>
            </a>
          </div>
          <div class="delete">
            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onSubmit="return checkDelete()">
              @csrf
              @method('DELETE')
              <div>
                <button type="submit"><i class="far fa-trash-alt fa-lg delete-icon"></i></button>
              </div>
            </form>
          </div>
          @endif
        </div>
      </div>
      <div class="body">
        <div class="main-body">
          <h1>{{ $post->title }}</h1>
          <!-- 改行を維持 -->
          @if(!empty($newContent))
            <p class="content">{!! nl2br(e(ltrim($newContent))) !!}</p>
          @else
            <p class="content">{!! nl2br(e($post->content)) !!}</p>
          @endif
          <div class="tag-buttons">
            <ul>
              @foreach($post->tags as $tag)
                <li>
                  <a href="{{ route('posts.index', ['tag_name' => $tag->tag_name]) }}">
                    #{{ $tag->tag_name }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
      <div class="sub-body">
        <p class="date">更新日：{{ $post->updated_at->format('Y/m/d') }}</p>
      </div>
    </div>

    <!-- コメント部分 -->
    <comment-component
      :post-id="{{ json_encode($post->id) }}"
      :user-id="{{ json_encode($user->id) }}"
      :user-name="{{ json_encode($user->name) }}"
      :profile-url="{{ json_encode($user->profileImg_url) }}"
      :comments-array="{{ json_encode($comments_array) }}"
    ></comment-component>
  </div>
</div>

@endsection