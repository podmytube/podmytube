<p class="mb-3 text-muted text-center">
	<a href="{{route('terms')}}">Terms</a> - <a href="{{ route('privacy') }}">Privacy</a>
	<p class="copyright text-muted">Copyright &copy; Podmytube 2020</p>
</p>

@include ('partials.tchat')

<script>
	$(document).ready(function() {
		$('.navbar-nav>li>a').on('click', function() {
			$('#navbarNav').collapse('hide');
		});
	});
</script>