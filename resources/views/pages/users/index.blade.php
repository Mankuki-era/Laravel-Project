@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
    ユーザー一覧
  </h1>
  @if($target_userid != $authuser_id)
  <ul class="back-link">
    <li><a href="{{ route('users.show', $target_userid) }}"><i class="fas fa-user"></i> {{ $target_username }}さんのページ</a></li>
  </ul>
  @endif
</div>
@if (session('status'))
  <div class="alert alert-success session-msg" role="alert">
      {{ session('status') }}
  </div>
@endif

<div class="user-indexPage">
  @if($showfollowers)
    <h2><span>{{ $target_username }}</span> さんのフォロワー</h2>
  @else
    <h2><span>{{ $target_username }}</span> さんがフォロー中</h2>
  @endif
  @if(empty($users))
    <div class="no-posts">
      <p><i class="fas fa-info-circle fa-lg info-icon"></i> 該当のユーザーはいません</p>
    </div>
  @endif
  <div class="usersCard-box">
    @foreach($users as $user)
    <?php
      $defaultFollowed = App\Following::where('user_id', $authuser_id)->where('following_user_id', $user[0]->id)->first();
      if(empty($defaultFollowed)) {
          $defaultFollowed = false;
      }
      else {
          $defaultFollowed = true;
      }
      $defaultfollowers_count = count(App\Following::where('following_user_id', $user[0]->id)->get());
      $defaultfollowing_count = count(App\Following::where('user_id', $user[0]->id)->get());
      $posts_count = count(App\Post::where('user_id', $user[0]->id)->get());
    ?>
    @if($user[0]->id == $authuser_id)
      <div class="userCard-item myCard-item">
    @else
      <div class="userCard-item">
    @endif
      <div class="profile-img">
        <img src="{{ $user[0]->profileImg_url }}" alt="プロフィール画像">
      </div>
      
      <div class="profile-secondary">
        @if($user[0]->id == $authuser_id)
        <div>
          <div class="user-name">
            <p class="name">{{ $authuser_name }}</p>
          </div>
          <div class="sub-content">
            <p>{{ $posts_count}} posts</p>
            <p><a href="{{ route('users.index', ['user_id' => $authuser_id, 'showfollowers' => true]) }}">{{ $defaultfollowers_count }} followers</a></p>
            <p><a href="{{ route('users.index', ['user_id' => $authuser_id, 'showfollowers' => false]) }}">{{ $defaultfollowing_count }} following</a></p>
          </div>
        </div>
        @else
        <follow-component
        :authuser-id="{{ json_encode($authuser_id) }}"
        :authuser-name="{{ json_encode($authuser_name) }}"
        :followuser-id="{{ json_encode($user[0]->id) }}"
        :followuser-name="{{ json_encode($user[0]->name) }}"
        :posts-count="{{ json_encode($posts_count) }}"
        :default-Followed="{{ json_encode($defaultFollowed) }}"
        :defaultfollowers-count="{{ json_encode($defaultfollowers_count) }}"
        :defaultfollowing-count="{{ json_encode($defaultfollowing_count) }}"
        ></follow-component>
        @endif
        <p class="profile-content">{{ $user[0]->profile_content }}</p>
      </div>
    </div>
    @endforeach
  </div>
</div>

@endsection