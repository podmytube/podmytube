@extends('layouts.app')

@section('pageTitle', $channel->channel_name . ' list of episodes')

@section('content')
    <div class="max-w-screen-xl mx-auto py-12 px-4">

        <h2 class="text-3xl md:text-5xl text-white font-semibold">Podcast episodes</h2>

        <!-- component -->
        <nav class="text-black font-bold my-8" aria-label="Breadcrumb">
            <ol class="list-none p-0 inline-flex text-gray-100">
                <li class="flex items-center">
                    <a href="{{ route('home') }}">Home</a>
                    <svg class="fill-current w-3 h-3 mx-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path
                            d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z" />
                    </svg>
                </li>
                <li>
                    <a href="#" class="text-gray-400" aria-current="page">{{ $channel->channel_name }} episodes</a>
                </li>
            </ol>
        </nav>

        <p class="text-center pb-6">
            @if ($channel->isPaying())
                <a href="{{ route('channel.medias.create', $channel) }}">
                    <button target="_blank" class="btn-podcast">
                        @php echo file_get_contents(public_path('images/gift.svg')) @endphp
                        Add exclusive content
                    </button>
                </a>
            @else
                <a href="{{ route('plans.index', $channel) }}">
                    <button target="_blank" class="btn-upgrade">
                        @php echo file_get_contents(public_path('images/rocket.svg')) @endphp
                        Upgrade now and add exclusive content. 😍
                    </button>
                </a>
            @endif
        </p>

        @if ($medias->count() > 0)
            <div class="bg-gray-100 mt-6 px-4 py-4 rounded-lg max-w-screen-lg md:mx-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-900 text-white">
                            <th class="p-3 border-white">Episode title</th>
                            <th class="p-3">Published</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($medias as $media)
                            <tr class="@if ($loop->even) bg-gray-200 @endif">
                                <td class="px-2 py-2">
                                    @if ($media->isUploadedByUser())
                                        <a
                                            href="{{ route('channel.medias.edit', ['channel' => $channel, 'media' => $media]) }}">
                                            {{ $media->title }} </a>
                                    @else
                                        {{ $media->title }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{ $media->publishedAt() }}
                                </td>
                                <td class="text-center">
                                    <span title="{{ $media->statusComment() }}"
                                        class="inline-flex items-center rounded-full bg-gray-900 px-3 py-0.5 text-sm font-medium text-gray-800">
                                        {{ $media->statusEmoji() }}</span>
                                </td>
                                <td class="text-center">
                                    @if ($media->isDisabled())
                                        <form method="POST" action="{{ route('media.enable', ['media' => $media]) }}">
                                            @method('PATCH')
                                            @csrf
                                            <button type="submit" class="btn-upgrade">enable</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('media.disable', ['media' => $media]) }}">
                                            @method('PATCH')
                                            @csrf
                                            <button type="submit" class="btn-disable">disable</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 px-6">
                    {{ $medias->appends(['nb' => $nbItemsPerPage])->links() }}
                </div>

            </div>
        @else
            <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
                <p class="font-bold">No episodes yet.</p>
                <p class="text-base">This is absolutely normal if you registered your channel only minutes ago, else you
                    should <a href="mailto:frederick@podmytube.com" class="underline">contact me</a>.</p>
            </div>
        @endif
    </div>
@endsection
