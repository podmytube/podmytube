<hr class="my-4">

<p class="mb-3 text-muted text-center">
	<a href="{{route('terms')}}">Terms</a> - <a href="{{ route('privacy') }}">Privacy</a>
	&copy; 2017-2020 <a href="https://www.podmytube.com">Podmytube</a>
</p>

@if (App::environment('production'))
<script async defer data-domain="podmytube.com" src="https://plausible.io/js/plausible.js"></script>
@endif
