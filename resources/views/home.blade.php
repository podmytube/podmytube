@extends('layouts.app')

@section('pageTitle', __('messages.page_title_home_index'))


@section('content')
    <div class="max-w-screen-xl mx-auto text-gray-100 py-12 px-4">
        <div class="flex flex-col sm:flex-row sm:items-start">
            <div class="flex-1">
                @include ('partials.channels')
            </div>
            <div class="flex-1">
                @include ('partials.playlists')
            </div>
        </div>
    </div>
    <!--/home main container-->
@endsection
