@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
      プロフィール編集画面
  </h1>
</div>
@if (session('status'))
  <div class="alert alert-success session-msg" role="alert">
      {{ session('status') }}
  </div>
@endif
<div class="profile-editPage">
  <div class="form-contena">
    <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <input type="hidden" name="id" value="{{ $user->id }}">
      <div class="form-group">
        <label for="name">名前</label>
        <div class="form-items">
          <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $user->name }}" required autofocus>
          
          @error('name')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
      </div>
      
      <div class="form-group">
        <label for="profile_content">プロフィール</label>
        <div class="form-items">
          <textarea id="profile_content" class="form-control @error('profile_content') is-invalid @enderror" name="profile_content" rows="3">{{ $user->profile_content }}</textarea>
          
          @error('profile_content')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
      </div>

      <div class="form-group image-form">
        <div class="form-items">
          <label for="image">プロフィール画像</label>
          <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
        </div>
        <img id="preview">
      </div>
      
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          更新する
        </button>
      </div>
    </form>
  </div>
</div>
@endsection