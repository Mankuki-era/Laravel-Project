<nav>
  <div class="container">
    <h1>
      <a href="{{ url('/') }}">
        𝓡𝓮𝓬𝓲𝓹𝓪𝓽𝓱𝔂
      </a>
    </h1>
    <div class="subLink-left">
      @auth
        <ul>
          <li>
            <a href="{{ route('posts.create') }}">
              <i class="fas fa-edit"></i> 新規投稿
            </a>
          </li>
          <li>
            <a href="{{ route('users.show', \Auth::id()) }}">
            <i class="fas fa-user"></i> マイページ
            </a>
          </li>
        </ul>
      @endauth
    </div>

    <div class="subLink-right">
      <ul>
        @guest
          <li>
            <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> {{ __('Login') }}</a>
          </li>
          @if (Route::has('register'))
            <li>
                <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> {{ __('Register') }}</a>
            </li>
          @endif
        @else
          <li>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> 
                {{ __('Logout') }}
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
              @csrf
            </form>
          </li>
        @endguest
      </ul>
    </div>

    <div class="mobile-menu">
      <div class="menu">
        <label for="menu_bar01"><i class="fas fa-ellipsis-h"></i></label>
        <input type="checkbox" id="menu_bar01" class="accordion" />
        <ul id="links01">
          @guest
            <li>
              <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> {{ __('Login') }}</a>
            </li>
            @if (Route::has('register'))
              <li>
                  <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> {{ __('Register') }}</a>
              </li>
            @endif
          @else
            <li>
              <a href="{{ route('posts.create') }}">
                <i class="fas fa-edit"></i> 新規投稿
              </a>
            </li>
            <li>
              <a href="{{ route('users.show', \Auth::id()) }}">
              <i class="fas fa-user"></i> マイページ
              </a>
            </li>
            <li>
              <a href="{{ route('logout') }}"
                  onclick="event.preventDefault();
                                  document.getElementById('logout-form').submit();">
                  <i class="fas fa-sign-out-alt"></i> 
                  {{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </li>
          @endauth
        </ul>
      </div>
    </div>

  </div>
</nav>