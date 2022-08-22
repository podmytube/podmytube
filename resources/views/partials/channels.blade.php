<!--/subhead row-->


@if (!$channels->count())
    <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
        <p class="font-bold">You have no podcast at this time.</p>
        <p class="text-base">It's time to <a href="{{ route('channel.step1') }}"
                class="border-b-2 border-gray-900">transform your channel into a podcast</a>.</p>
    </div>
@else
    <h2 class="h2">
        Your @if ($channels->count() > 1)
            podcasts
        @else
            podcast
        @endif
    </h2>

    <div class="flex flex-col sm:flex-row">
        @foreach ($channels as $channel)
            <div class="mx-auto md:mx-1 max-w-md rounded-lg overflow-hidden shadow-lg bg-gray-100 mt-4 py-4 md:pt-10 ">
                <img class="mx-auto shadow-lg rounded-lg" src="{{ $channel->vignetteUrl }}"
                    alt="Your best cover for {{ $channel->title() }}">
                <div class="px-2 md:px-6 pt-4 py-2 text-center">
                    <div class="font-bold text-xl text-gray-900 leading-tight">{{ $channel->title() }}</div>
                </div>

                @if (!$channel->hasSubscription() || $channel->isFree())
                    <p class="text-center pb-6">
                        <a href="{{ route('plans.index', $channel) }}">
                            <button target="_blank" class="btn-upgrade">
                                @php echo file_get_contents(public_path('images/rocket.svg')) @endphp
                                Upgrade
                            </button>
                        </a>
                    </p>
                @else
                    <p class="text-gray-700 text-base text-center">Your plan :
                        {{ $channel->subscription?->plan?->name ?? 'none' }}
                    </p>
                @endif

                <p class="text-center py-2">
                <p class="m-2 p-2 rounded-lg text-xl bg-gray-900 text-gray-100 text-center">⚡ <a
                        href="{{ route('analytics', $channel) }}">Downloads</a> ⚡</p>
                <ul class="text-gray-700 text-center list-disc">
                    <li>this week : {{ $channel->thisWeekDownloads }}</li>
                    <li>this month : {{ $channel->thisMonthDownloads }}</li>
                </ul>
                </p>

                <div class="text-center pb-6">
                    <a href="{{ $channel->podcastUrl() }}" target="_blank">
                        <button class="btn-podcast">
                            @php echo file_get_contents(public_path('images/podcast.svg')) @endphp
                            <span class="px-2">Your podcast feed</span>
                        </button>
                    </a>
                </div>

                <div class="px-4">
                    <div class="flex justify-center items-center">
                        <a href="{{ route('channel.edit', $channel) }}">
                            <button
                                class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2 rounded-l-lg">
                                @php echo file_get_contents(public_path('images/edit.svg')) @endphp
                                Podcast
                            </button>
                        </a>
                        <a href="{{ route('channel.cover.edit', $channel) }}">
                            <button
                                class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2">
                                @php echo file_get_contents(public_path('images/cover.svg')) @endphp
                                Cover
                            </button>
                        </a>
                        <a href="{{ route('channel.medias.index', $channel) }}">
                            <button
                                class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2 rounded-r-lg">
                                @php echo file_get_contents(public_path('images/list.svg')) @endphp
                                Episodes
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
