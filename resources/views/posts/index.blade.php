@extends('layouts.app')

@section('pageTitle', __('medias.page_title_index') )

@section ('content')

<div class="mx-auto">
    <div class="container pb-6 md:pb-12 mx-auto">
        <div class="text-center mt-12 mb-8">
            <h1 class="text-3xl md:text-5xl text-white font-semibold">Blog</h1>
        </div>

        @if ($posts->count())
        <section class="grid grid-cols-2">
        @foreach($posts as $post)
        <div class="p-4">
            <div class="p-4 rounded-lg bg-gray-800 text-white md:min-h-full">
                <a href="{{ route('post.show', $post) }}" class="block text-xl mb-4 leading-tight font-bold no-underline hover:text-red-400 md:text-2xl">{!! $post->title() !!}</a>
                <p class="text-gray-500 text-sm md:text-base">{!! $post->excerpt !!}</p>
                <a href="{{ route('post.show', $post) }}" class="text-base md:text-lg font-semibold md:border-b-2 md:border-transparent md:hover:border-red-400 md:hover:border-b-2 hover:text-red-400">Read more&nbsp;→</a>
            </div>
        </div>
        @endforeach
        </section>
        @endif
    </div>
</div>

@endsection