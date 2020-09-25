@extends('layouts.app')

@section('content')
<div class="pageTitle">
  <h1>
      投稿作成画面
  </h1>
</div>
<div class="createPage">
  @if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
  @endif
  @if ($errors->any())
    <div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    </div>
  @endif
  <div class="form-contena">
    <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-group">
        <label for="title">タイトル</label>
        <div class="form-items">
          <input id="title" type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required autofocus>
          
          @error('title')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
      </div>
      
      <div class="form-group">
        <label for="content">本文</label>
        <div class="form-items">
          <textarea id="content" class="form-control @error('content') is-invalid @enderror" name="content" rows="5" required>{{ old('content') }}</textarea>
          
          @error('content')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
      </div>

      <div class="form-group image-form">
        <div class="form-items">
          <label for="image">画像</label>
          <input type="file" class="form-control-file" id="image" name="image" accept="image/*">
        </div>
        <img id="preview" class="preview">
      </div>
      
      <div class="form-group">
        <button type="submit" class="btn btn-primary">
          投稿する
        </button>
      </div>
    </form>
  </div>
</div>
@endsection