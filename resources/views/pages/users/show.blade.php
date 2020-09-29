@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
      ユーザー詳細画面
  </h1>
</div>
@if (session('status'))
  <div class="alert alert-success session-msg" role="alert">
      {{ session('status') }}
  </div>
@endif
<div class="user-showPage">
  <div class="userShow-contena">
    <div class="profile">
      <img src="{{ $user->profileImg_url }}" alt="プロフィール画像">
      <div class="profile-secondary">
        @if($user->id == \Auth::id())
        <div class="user-name">
          <p class="name">{{ $user->name }}</p>
          <div class="edit-link">
            <a href="{{ route('users.edit', $user->id) }}"><i class="fas fa-user-cog"></i> プロフィール編集</a>
          </div>
        </div>
        <div class="sub-content">
          <p>{{ $posts_count }} posts</p>
          <p><a href="{{ route('users.index', ['user_id' => $user->id, 'showfollowers' => true]) }}">{{ $defaultfollowers_count }} フォロワー</a></p>
          <p><a href="{{ route('users.index', ['user_id' => $user->id, 'showfollowers' => false]) }}">{{ $defaultfollowing_count }} フォロー中</a></p>
        </div>
        @else
          <follow-component
          :authuser-id="{{ json_encode($authuser_id) }}"
          :authuser-name="{{ json_encode($authuser_name) }}"
          :followuser-id="{{ json_encode($user->id) }}"
          :followuser-name="{{ json_encode($user->name) }}"
          :posts-count="{{ json_encode($posts_count) }}"
          :default-Followed="{{ json_encode($defaultFollowed) }}"
          :defaultfollowers-count="{{ json_encode($defaultfollowers_count) }}"
          :defaultfollowing-count="{{ json_encode($defaultfollowing_count) }}"
          ></follow-component>
        @endif
        <p class="profile-content">{{ $user->profile_content }}</p>
      </div>
    </div>
    @if(count($posts) == 0)
    <div class="no-posts">
      <p><i class="fas fa-info-circle fa-lg info-icon"></i> まだ投稿がありません</p>
    </div>
    @endif
    @if(count($posts) > 1)
    <div class="user-posts">
    @else
    <div class="user-post">
    @endif
        @foreach($posts as $post)
        <?php 
          $newContent = '';
          if(isset($post->tags)) {
            $target = [];
            foreach($post->tags as $tag) {
              array_push($target, '#' . $tag->tag_name);
            }
            $newContent = str_replace($target, '', $post->content);
          }
        ?>
        <div class="card-item">
          <a href="{{ route('posts.show', $post->id) }}" class="image">
            <img src="{{ $post->img_url }}" alt="画像">
            <div class="mask">
              <div class="caption">
                <p>
                  <span><i class="fas fa-heart fa-lg heart-icon"></i> {{ count($post->likes) }}</span>
                  <span><i class="fas fa-lg fa-comment"></i> {{ count($post->comments) }}</span>    
                </p> 
              </div>
            </div>
          </a>
          <div class="body">
            <h1>{{ $post->title }}</h1>
            @if(!empty($newContent))
              <p class="content">{{ $newContent }}</p>
            @else
              <p class="content">{{ $post->content }}</p>
            @endif
            <div class="tag-buttons">
              <ul>
                @foreach($post->tags as $tag)
                  <li>
                    <a href="{{ route('posts.index', ['tag_name' => $tag->tag_name]) }}">#{{ $tag->tag_name }}</a>
                  </li>
                @endforeach
              </ul>
            </div>
            <p class="date">更新日：{{ $post->updated_at->format('Y/m/d') }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</div>
@endsection