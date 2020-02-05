
@if(session('message'))

<div class="alert {{ session('messageClass', 'alert-primary') }}" role="alert">
	{{ session('message') }}
</div>

@endif