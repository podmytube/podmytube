@include ('partials.tchat')
<footer class="container">
	<div class="row text-muted justify-content-center">
		<div class="col-lg-8 col-md-10 mx-auto text-center">
			Copyright &copy; Podmytube 2020 - <a href="{{route('terms')}}">Terms</a> - <a href="{{ route('privacy') }}">Privacy</a>
		</div>
	</div>
</footer>
<script>
	$(document).ready(function() {
		$('.navbar-nav>li>a').on('click', function() {
			$('#navbarNav').collapse('hide');
		});
	});
</script>