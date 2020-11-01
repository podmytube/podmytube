@extends('layouts.app')

@section('pageTitle', __('medias.page_title_index') )

@section ('content')

<div class="max-w-screen-xl mx-auto mt-4 mb-16">

    <h1 class="text-center text-3xl pt-12 pb-6 md:text-5xl text-white font-semibold">Blog</h1>

    <div>
        @if ($posts->count())
        <div class="flex flex-wrap px-2">
            @foreach($posts as $post)
            <div class="w-full md:w-1/2 py-4">
                <div class="px-4">
                    <div class="p-4 rounded-lg bg-gray-800 text-white md:min-h-full">
                        <a href="{{ route('post.show', $post) }}" 
                            class="block text-xl mb-4 leading-tight font-bold no-underline md:inline-block md:border-b-2 md:border-transparent md:hover:border-white md:text-2xl">
                            {!! $post->title() !!}</a>
                        <p class="text-gray-100 text-sm md:text-base">
                            <a href="{{ route('post.show', $post) }}" class="no-underline">
                                {!! $post->excerpt !!}
                            </a>
                        </p>
                        <a href="{{ route('post.show', $post) }}" 
                            class="text-base md:text-lg font-semibold md:border-b-2 md:border-transparent md:hover:border-white md:hover:border-b-2">
                            Read more&nbsp;→</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@endsection