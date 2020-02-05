<nav class="navbar navbar-expand-sm navbar-light bg-light">
	<a class="navbar-brand pmt-logo" href="{{ url('/') }}"> </a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainCollapsingNav" aria-controls="mainCollapsingNav" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>

	<div class="collapse navbar-collapse" id="mainCollapsingNav">
		<ul class="navbar-nav ml-auto">
			@guest
			<li class="nav-item"><a href="{{ route('login') }}" class="nav-link">{{ __('messages.page_title_user_login') }}</a></li>
			<li class="nav-item"><a href="{{ route('register') }}" class="nav-link">{{__('messages.page_title_user_register')}}</a></li>
			@else
			<li class="nav-item">
				<a class="nav-link" href="{{ route('channel.create') }}"> {{ __('messages.button_create_channel_label') }} </a>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle" href="#" id="accountDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }}</a>
				<div class="dropdown-menu dropdown-menu-right" aria-labelledby="accountDropdown">
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
	</div>
</nav>