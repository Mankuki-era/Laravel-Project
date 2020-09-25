@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
      投稿一覧画面
  </h1>
</div>

<div class="indexPage">
  @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
  @endif

  @if(isset($tag_name))
    <div class="tag-message">
      <p><span>#{{ $tag_name }}</span> 関連の投稿数：{{ count($posts) }}件</p>
    </div>
  @endif
  <div class="card-box">
      
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

      $user = \Auth::user();
      $defaultCount = count($post->likes);
      $defaultLiked = $post->likes->where('user_id', $user->id)->first();
      if(empty($defaultLiked)) {
          $defaultLiked = false;
      }
      else {
          $defaultLiked = true;
      }
    ?>
    <div class="card-item">
      <div class="card-header">
        <div class="user">
          <a href="{{ route('users.show', $post->user->id) }}" class="auth-name">
            @if(isset($post->user->profileImg_url)) 
              <img src="{{ $post->user->profileImg_url }}" alt="プロフィール画像"><span>{{ $post->user->name }}</span>
            @else
              <img src="/images/no-image.png" alt="プロフィール画像">
            @elseif
          </a>
        </div>
        <p class="date">{{ $post->updated_at->format('Y/m/d') }}</p>
      </div>
      <a href="{{ route('posts.show', $post->id) }}" class="image">
        <img src="{{ $post->img_url }}" alt="画像">
      </a>
      <div class="icons">
        <div class="left-icons">
          <div class="heart">
            <like-component
              :post-id="{{ json_encode($post->id) }}"
              :user-id="{{ json_encode($user->id) }}"
              :default-Count="{{ json_encode($defaultCount) }}"
              :default-Liked="{{ json_encode($defaultLiked) }}"
              :index-page="{{ json_encode($index_page) }}"
            ></like-component>
          </div>
          @if($post->user == \Auth::user())
          <div class="edit">
            <a href="{{ route('posts.edit', $post->id) }}" class="edit-link">
              <i class="far fa-edit edit-icon"></i>
            </a>
          </div>
          <div class="delete">
            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onSubmit="return checkDelete()">
              @csrf
              @method('DELETE')
              <div>
                <button type="submit"><i class="far fa-trash-alt delete-icon"></i></button>
              </div>
            </form>
          </div>
          @endif
        </div>
      </div>
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
      </div>
      <div class="sub-body">
        <a href="{{ route('posts.show', $post->id) }}">
          <i class="fas fa-chevron-circle-right"></i> 詳細を見る
        </a>
      </div>
    </div>
    @endforeach
  </div>
</div>

@endsection
        