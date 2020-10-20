    
<!--/subhead row-->


@if (count($channels))
    <h2 class="text-3xl md:text-5xl text-white font-semibold">Your podcast</h2>
    
    @foreach ($channels as $channel)

    <div class="max-w-md rounded-lg overflow-hidden shadow-lg bg-gray-100 mt-4 pt-4 md:pt-10 pb-4 ">
        <img class="mx-auto shadow rounded-lg" src="{{$channel->vigUrl}}" alt="Cover for {{ $channel->title() }}">
        <div class="px-2 md:px-6 py-4 text-center">
            <div class="font-bold text-xl leading-tight mb-2">{{ $channel->title() }}</div>
            <p class="text-gray-700 text-base">Your plan : {{ $channel->subscription->plan->name }}</p>
        </div>

        @if ($channel->isFree())
        <p class="text-center pb-6">
            <a href="{{ route('plans.index', $channel) }}">
                <button target="_blank" class="bg-green-800 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded-lg">
                    <svg  class="h-6 w-auto inline fill-current" 
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 382.7 382.7">
                        <path d="M375.9 0.1c-0.8-0.1-1.6-0.1-2.4 0 -74.3 14.8-142.6 50.9-196.6 104l-70.6 3.6c-2 0.1-3.9 0.9-5.3 2.3l-72.5 72.5c-3.1 3.1-3.2 8.2 0 11.3 1.5 1.5 3.6 2.4 5.7 2.4h0.6l81.7-5.6c-3.2 6.7-6.2 13.6-9 20.8 -1.2 3-0.5 6.3 1.8 8.6l53.8 53.8c2.3 2.2 5.6 2.9 8.6 1.8 7.4-2.9 14.5-6.1 21.4-9.4l-5.6 82.6c-0.3 4.4 3.1 8.2 7.5 8.4 2.3 0.1 4.5-0.7 6.1-2.3l72.5-72.5c1.4-1.4 2.2-3.3 2.3-5.3l3.7-72c52.6-53.9 88.5-121.9 103.1-195.8C383.2 4.9 380.2 0.8 375.9 0.1zM55.1 178.4l55.2-55.2 51.3-2.6c-14 16.4-26.2 34.2-36.6 53L55.1 178.4zM260 273l-55.2 55.2 4.8-70.9c18.9-10.4 36.7-22.7 53-36.7L260 273zM170.6 258.4l-46.3-46.3c58.3-142.5 199.1-184.5 240-193.8C355.1 59.3 313.3 200 170.6 258.4z"/>
                        <path d="M88.4 223.2c-3.1-3.2-8.2-3.2-11.4-0.1l-74.2 73.9c-3.4 2.9-3.8 7.9-0.9 11.3 1.6 1.9 4 2.9 6.5 2.8 2.1 0 4.2-0.8 5.7-2.3l74.2-74.2C91.5 231.5 91.5 226.4 88.4 223.2z"/>
                        <path d="M90.2 292.6c0 0 0 0-0.1-0.1 -3.1-3.1-8.2-3.1-11.3 0l-74.3 74.6c-3.8 2.3-5.1 7.2-2.8 11 2.3 3.8 7.2 5.1 11 2.8 1.2-0.7 2.1-1.6 2.8-2.8l74.2-74.2C93 300.9 93.2 295.8 90.2 292.6z"/>
                        <path d="M158.7 294.3c-3-2.6-7.4-2.6-10.4 0h0.2l-74.2 74.2c-3.4 2.9-3.7 7.9-0.9 11.3 2.9 3.4 7.9 3.7 11.3 0.9 0.3-0.3 0.6-0.6 0.9-0.9l74-74.2C162.4 302.3 162 297.2 158.7 294.3z"/>
                        <path d="M277.6 69.5c-19.6 0-35.5 15.9-35.5 35.5 0 9.4 3.7 18.5 10.4 25.1 6.6 6.7 15.7 10.4 25.1 10.4 19.6 0 35.5-15.9 35.5-35.5C313.1 85.4 297.2 69.5 277.6 69.5zM291.5 119.1c-7.8 7.6-20.2 7.6-27.9 0 -7.6-7.6-7.6-20 0-27.6v-0.3c7.7-7.7 20.2-7.7 27.9 0C299.2 98.9 299.2 111.4 291.5 119.1z"/>
                    </svg>
                    Upgrade
                </button>
            </a>
        </p>
        @endif

        <div class="text-center pb-6">
            <a href="{{ $channel->podcastUrl() }}" target="_blank">
                <button class="bg-purple-800 hover:bg-purple-900 text-gray-100 font-bold py-2 px-4 rounded-lg">
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
            </a>
        </div>

        <div class="px-4">
            <div class="flex content-center">
                <!--a href="{{ route('channel.show', $channel) }}">
                    <button class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l-lg"> View </button>
                </a-->
                <a href="{{ route('channel.edit', $channel) }}">
                    <button class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l-lg"> Edit podcast </button>
                </a>
                <a href="{{ route('channel.thumbs.edit', $channel) }}">
                    <button class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-r-lg"> Update cover </button>
                </a>
                <!--a href="{{ route('channel.medias.index', $channel) }}">
                    <button class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-r-lg"> Episodes </button>
                </a-->
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