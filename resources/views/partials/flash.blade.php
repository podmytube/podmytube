
@if(!empty($errors) && (session('success') || session('info') || session('warning') || $errors->any()))
<div class="container items-center mx-auto">
	@if(session('success'))
	<div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
		<p class="font-semibold">🎉 Success ! 🍾</p>
		<p>{{ session("success") }}</p>
	</div>
	@endif

	@if ($errors->any())
	<div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
		<p class="font-semibold">Errors !</p>
		<p class="font-bold">
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</p>
	</div>
	@endif

	@if(session('warning'))
	<div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
		<p class="font-semibold">Warning !</p>
		<p class="font-bold">{{ session("warning") }}</p>
	</div>
	@endif

	@if(session('info'))
	<div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
		<p class="font-bold">{{ session("info") }}</p>
	</div>
	@endif
</div>
@endif