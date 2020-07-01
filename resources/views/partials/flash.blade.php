@if(!empty($errors) && (session('success') || session('info') || session('warning') || $errors->any()))
<div class="mt-2 mb-0">
	@if(session('success'))
	<div class="alert alert-success alert-block">
		<button type="button" class="close" data-dismiss="alert">×</button>
		{{ session("success") }}
	</div>
	@endif

	@if ($errors->any())
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">×</button>
		<ul>
			@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
			@endforeach
		</ul>
	</div>
	@endif

	@if(session('warning'))
	<div class="alert alert-warning alert-block">
		<button type="button" class="close" data-dismiss="alert">×</button>
		{{ session("warning") }}
	</div>
	@endif

	@if(session('info'))
	<div class="alert alert-info alert-block">
		<button type="button" class="close" data-dismiss="alert">×</button>
		{{ session("info") }}
	</div>
	@endif
</div>
@endif