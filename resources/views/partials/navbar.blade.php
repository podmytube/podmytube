{{--
<!--
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
-->
--}}

<!--Nav-->
<nav id="header" class="fixed w-full z-30 top-0 text-white">

	<div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 py-2">
			
		<div class="pl-4 flex items-center">
            <a class="toggleColour text-white no-underline hover:no-underline font-bold text-2xl lg:text-4xl"  href="{{route('www.index')}}"> 
                <img class="h-16 inline m-auto" src="/images/podmytube-logo.svg" alt="create your podcast from your youtube channel - Podmytube" /> 
			</a>
		</div>

		<div class="block lg:hidden pr-4">
			<button id="nav-toggle" class="flex items-center p-1 text-orange-800 hover:text-gray-900">
				<svg class="fill-current h-6 w-6" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <title>Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                </svg>
			</button>
		</div>

		<div class="w-full flex-grow lg:flex lg:items-center lg:w-auto hidden lg:block mt-2 lg:mt-0 bg-white lg:bg-transparent text-black p-4 lg:p-0 z-20" id="nav-content">
			<ul class="list-reset lg:flex justify-end flex-1 items-center">
				<li class="mr-3">
					<a class="inline-block py-2 px-4 text-black font-bold no-underline" href="#">
                        Active
                    </a>
				</li>
				<li class="mr-3">
					<a class="inline-block text-black no-underline hover:text-gray-800 hover:text-underline py-2 px-4" href="#">link</a>
				</li>
				<li class="mr-3">
					<a class="inline-block text-black no-underline hover:text-gray-800 hover:text-underline py-2 px-4" href="#">link</a>
				</li>
			</ul>
			<button id="navAction" class="mx-auto lg:mx-0 hover:underline bg-white text-gray-800 font-bold rounded-full mt-4 lg:mt-0 py-4 px-8 shadow opacity-75">Action</button>
		</div>
	</div>
	
	<hr class="border-b border-gray-100 opacity-25 my-0 py-0" />
</nav>