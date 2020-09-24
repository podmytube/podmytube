<nav class="navbar navbar-expand-sm navbar-light fixed-top navbar-default bg-light py-0">
    <a class="navbar-brand" href="{{ route('www.index') }}">
        <img src="/images/podmytube-logo.svg" width="80" alt="{{ __('messages.navbar_podmytube_logo') }} • Podmytube" />
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="navbar-collapse collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('www.index') }}#main">
                    Home
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if( in_array(\Request::route()->getName(), ['post.index','post.show']))active @endif" href="{{ route('post.index') }}">
                    Blog
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('www.index') }}#features">
                    Features
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('www.index') }}#pricing">
                    Plans
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('www.index') }}#about">
                    About
                </a>
            </li>
        </ul>
        <ul class="navbar-nav ml-auto">
            @guest
            <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">{{ __('messages.page_title_user_login') }}</a></li>
            <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">{{__('messages.page_title_user_register')}}</a></li>
            @else
            <li class="nav-item"><a class="nav-link" href="{{ route('channel.create') }}"><i class="fas fa-plus"></i> {{ __('messages.button_create_channel_label') }} </a></li>
            <li class="nav-item"><a class="nav-link" href="mailto:contact@podmytube.com"><i class="fas fa-envelope-square"></i> {{ __('messages.button_need_assistance_label') }} </a></li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-door-open"></i>
                    {{ __('messages.page_title_user_logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="accountDropdown">
                    <a class="dropdown-item" href="{{ route('user.show', auth()->user() ) }}">{{ __('messages.page_title_user_show') }}</a>
                    <a class="dropdown-item" href="{{ route('password.form') }}">{{ __('messages.change_password_label') }}</a>
                </div>
            </li>
            @endguest
        </ul>
    </div>
</nav>