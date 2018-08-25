<option value="">Faites votre choix</option>
@if ( count ($categories) > 0 )
    @foreach ($categories as $key)
        <option value="{{ $key }}"{{ htmlspecialchars($selCat) == $key ? ' selected' : '' }}> {{ $key }} </option>
    @endforeach
@endif
