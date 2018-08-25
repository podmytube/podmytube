<nav class="navbar navbar-expand-lg">
	<!-- Branding Image -->
	<a class="navbar-brand" href="{{ url('/') }}"> <img src="/images/logo-small.png" class="navbar-left" /> </a>
	<!-- Left Side Of Navbar -->
	<ul class="navbar-nav mr-auto">
<!--
		<li class="nav-item active">
			<a class="nav-link">Left Link 1</a>
		</li>
		<li class="nav-item">
			<a class="nav-link">Left Link 2</a>
		</li>
-->
	</ul>

	<!-- Right Side Of Navbar -->
	<!-- Authentication Links -->
	<ul class="navbar-nav ml-auto">
	@guest
		<li class="nav-item"><a href="{{ route('login') }}" class="nav-link">{{ __('messages.page_title_user_login') }}</a></li>
		<li class="nav-item"><a href="{{ route('register') }}" class="nav-link">{{__('messages.page_title_user_register')}}</a></li>
	@else
		<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
			<div class="dropdown-menu dropdown-menu-right">
				<a class="dropdown-item" href="{{ route('home') }}">{{ __('messages.page_title_home_breadcrumb') }}</a>
                <a class="dropdown-item" href="{{ route('user.show') }}">{{ __('messages.page_title_user_show') }}</a>
				<a class="dropdown-item" href="{{ route('password.form') }}">{{ __('messages.change_password_label') }}</a>
				<a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('messages.page_title_user_logout') }}</a>
				<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
						{{ csrf_field() }}
				</form>
			</div>
		</li>
	@endguest
	</ul>
</nav>


