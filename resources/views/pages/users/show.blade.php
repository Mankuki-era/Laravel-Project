@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
      ユーザー詳細画面
  </h1>
</div>
<div class="user-showPage">
  @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
  @endif
  <div class="userShow-contena">
    <div class="profile">
      @if(isset($user->profileImg_url)) 
        <img src="{{ $user->profileImg_url }}" alt="プロフィール画像">
      @else
        <img src="/images/no-image.png" alt="プロフィール画像">
      @endif
      <div class="profile-secondary">
        <div class="user-name">
          <p class="name">{{ $user->name }}</p>
          @if($user->id == \Auth::id())
            <div class="edit-link">
              <a href="{{ route('users.edit', $user->id) }}"><i class="fas fa-user-cog"></i> プロフィール編集</a>
            </div>
          @endif
        </div>
        <div class="sub-content">
          <p>{{ count($user->posts) }} posts</p>
          <p>100 followers</p>
          <p>130 following</p>
        </div>
        <p class="profile-content">{{ $user->profile_content }}</p>
      </div>
    </div>
      <div class="user-posts">
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