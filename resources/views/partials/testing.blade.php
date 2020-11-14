<div>
    route : {{\Request::route()->getName()}}

    @if ($errors->any())
    -- errors --\n
    @foreach ($errors->all() as $error)
    - {{ $error }}<br>
    @endforeach
    @endif
</div>