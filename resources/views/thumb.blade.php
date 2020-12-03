@extends('layouts.app')

@section('pageTitle', __('messages.page_title_home_index') )


@section('content')

<div class="max-w-screen-xl mx-auto text-gray-100 my-10 py-32 px-4 border-white border-2">
    for youtube
    <div class="flex items-center justify-center">
        <div>
            <svg class="h-40 w-auto inline fill-current" viewBox="0 0 128 128"
                xmlns="http://www.w3.org/2000/svg">
                <g>
                    <path d="m80.484 70.556a1.752 1.752 0 0 0 2.448-.369 23.509 23.509 0 1 0 -37.86 0 1.75 1.75 0 1 0 2.816-2.078 20.008 20.008 0 1 1 32.226 0 1.752 1.752 0 0 0 .37 2.447z"/>
                    <path d="m44.4 83.782a1.75 1.75 0 0 0 2.033-2.849 30.3 30.3 0 1 1 35.139 0 1.75 1.75 0 0 0 2.028 2.85 33.8 33.8 0 1 0 -39.2 0z"/>
                    <path d="m96.048 102.051h-18.68v-22.569a13.369 13.369 0 0 0 -6.78-11.617 13.368 13.368 0 1 0 -13.176 0 13.369 13.369 0 0 0 -6.78 11.617v22.569h-18.68a1.75 1.75 0 0 0 0 3.5h64.1a1.75 1.75 0 0 0 0-3.5zm-41.916-45.8a9.868 9.868 0 1 1 9.868 9.864 9.879 9.879 0 0 1 -9.868-9.868zm0 23.235a9.868 9.868 0 0 1 19.736 0v22.569h-19.736z"/>
                </g>
            </svg>
        </div>
        <div class="font-semibold text-5xl">Pod<span class="border-white border-b">my</span>tube</div>
    </div>
</div>

<div class="max-w-screen-sm mx-auto text-gray-100 my-10 py-32 px-4 border-white border-2">
    for mobile
    <div class="flex items-center justify-center">
        <div>
            <svg class="h-32 w-auto inline fill-current" viewBox="0 0 128 128"
                xmlns="http://www.w3.org/2000/svg">
                <g>
                    <path d="m80.484 70.556a1.752 1.752 0 0 0 2.448-.369 23.509 23.509 0 1 0 -37.86 0 1.75 1.75 0 1 0 2.816-2.078 20.008 20.008 0 1 1 32.226 0 1.752 1.752 0 0 0 .37 2.447z"/>
                    <path d="m44.4 83.782a1.75 1.75 0 0 0 2.033-2.849 30.3 30.3 0 1 1 35.139 0 1.75 1.75 0 0 0 2.028 2.85 33.8 33.8 0 1 0 -39.2 0z"/>
                    <path d="m96.048 102.051h-18.68v-22.569a13.369 13.369 0 0 0 -6.78-11.617 13.368 13.368 0 1 0 -13.176 0 13.369 13.369 0 0 0 -6.78 11.617v22.569h-18.68a1.75 1.75 0 0 0 0 3.5h64.1a1.75 1.75 0 0 0 0-3.5zm-41.916-45.8a9.868 9.868 0 1 1 9.868 9.864 9.879 9.879 0 0 1 -9.868-9.868zm0 23.235a9.868 9.868 0 0 1 19.736 0v22.569h-19.736z"/>
                </g>
            </svg>
        </div>
        <div class="font-semibold text-4xl">Pod<span class="border-white border-b">my</span>tube</div>
    </div>
</div>
    
<!--/home main container-->
@endsection