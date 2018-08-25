@if(Session::has('message'))

<div class="alert {{ Session::get('alert-class', 'alert-info') }}">
	{{ Session::get('message') }}
</div>

@endif