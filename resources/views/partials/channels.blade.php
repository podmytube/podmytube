    
<!--/subhead row-->


@if (count($channels))
    <h2 class="text-3xl md:text-5xl text-white font-semibold">Your podcast</h2>
    
    @foreach ($channels as $channel)

    <div class="max-w-md rounded-lg overflow-hidden shadow-lg bg-gray-100 p-2 ">
        <img class="mx-auto shadow" src="{{$channel->vigUrl}}" alt="Cover for {{ $channel->title() }}">
        <div class="px-6 py-4 text-center">
            <div class="font-bold text-xl mb-2">{{ $channel->title() }}</div>
            <p class="text-gray-700 text-base">Your plan : {{ $channel->subscription->plan->name }}</p>
        </div>

        @if($channel->subscription->plan->id==\App\Plan::FREE_PLAN_ID)
        <p>
            <a class="btn btn-success text-center" href="{{ route('plans.index', $channel) }}" role="button">
                <i class="fas fa-rocket"></i>
                {{ __('messages.button_i_want_to_upgrade_now') }}
            </a>
        </p>
        @endif

        <p class="text-center pb-2">
            <button href="{{ $channel->podcastUrl() }}" target="_blank" class="bg-purple-900 hover:bg-purple-800 text-gray-100 font-bold py-2 px-4 rounded-lg">
                <svg class="h-6 w-auto inline fill-current" 
                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M256 126c-63.4 0-115 51.6-115 115 0 30.4 11.9 58.1 31.2 78.6 2.9-10.5 7.9-20.5 14.6-29.4C176.9 276.4 171 259.4 171 241c0-46.9 38.1-85 85-85s85 38.1 85 85c0 18.4-5.9 35.4-15.8 49.3 6.8 8.9 11.7 18.8 14.6 29.4C359.1 299.1 371 271.4 371 241 371 177.6 319.4 126 256 126z"/>
                    <path d="M256 66c-96.5 0-175 78.5-175 175 0 67.6 38.5 126.3 94.7 155.5l-5.5-38.7C134.3 331.4 111 288.9 111 241c0-80 65-145 145-145s145 65 145 145c0 47.9-23.3 90.4-59.2 116.8l-5.5 38.7C392.5 367.3 431 308.6 431 241 431 144.5 352.5 66 256 66z"/>
                    <path d="M407 0H105C47.1 0 0 47.1 0 105v302c0 57.9 47.1 105 105 105h302c57.9 0 105-47.1 105-105V105C512 47.1 464.9 0 407 0zM482 407c0 41.4-33.6 75-75 75H105c-41.4 0-75-33.6-75-75V105c0-41.4 33.6-75 75-75h302c41.4 0 75 33.6 75 75V407z"/>
                    <path d="M256 186c-24.8 0-45 20.2-45 45s20.2 45 45 45 45-20.2 45-45S280.8 186 256 186zM256 246c-8.3 0-15-6.7-15-15s6.7-15 15-15 15 6.7 15 15S264.3 246 256 246z"/>
                    <path d="M306.6 299.1C293.9 284.4 275.4 276 256 276s-37.9 8.4-50.6 23.1c-12.7 14.7-18.5 34.2-15.7 53.4l7.8 54.8C200.6 429.4 219.8 446 242 446h28c22.3 0 41.4-16.6 44.5-38.6l7.8-54.8C325.1 333.3 319.4 313.8 306.6 299.1zM292.7 348.3l-7.8 54.8c-1 7.3-7.4 12.9-14.8 12.9H242c-7.4 0-13.8-5.5-14.8-12.9l-7.8-54.8c-1.5-10.6 1.6-21.4 8.7-29.5C235.1 310.7 245.3 306 256 306c10.7 0 20.9 4.7 28 12.8C291 326.9 294.2 337.6 292.7 348.3z"/>
                </svg>
                Your podcast feed
            </button>
        </p>

        <div class="px-2">
            <div class="flex content-center">
                <button href="{{ route('channel.show', $channel) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l-lg">
                    View
                </button>
                <button href="{{ route('channel.edit', $channel) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4">
                    Edit
                </button>
                <button href="{{ route('channel.thumbs.edit', $channel) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4">
                    Cover
                </button>
                <button href="{{ route('channel.medias.index', $channel) }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-r-lg">
                    Episodes
                </button>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
    <p class="font-bold">You have no channel at this time.</p>
    <p class="text-base">Add a new podcast.</p>
</div>
@endif