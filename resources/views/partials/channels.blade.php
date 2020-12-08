<!--/subhead row-->


@if (count($channels))
    <h2 class="text-3xl md:text-5xl text-white font-semibold">
    
    @if($channels->count()>1)
        Your podcasts
    @else
        Your podcast
    @endif
    </h2>
    
    @foreach ($channels as $channel)
    
    <div class="mx-auto md:mx-0 max-w-md rounded-lg overflow-hidden shadow-lg bg-gray-100 mt-4 pt-4 md:pt-10 pb-4 ">
        <img class="mx-auto shadow-lg rounded-lg" src="{{ $channel->vignetteUrl }}" alt="Your best cover for {{ $channel->title() }}">
        <div class="px-2 md:px-6 py-4 text-center">
            <div class="font-bold text-xl text-gray-900 leading-tight mb-2">{{ $channel->title() }}</div>
            <p class="text-gray-700 text-base">Your plan : {{ $channel->subscription->plan->name }}</p>
        </div>

        @if ($channel->isFree() && false)
        <p class="text-center pb-6">
            <a href="{{ route('plans.index', $channel) }}">
                <button target="_blank" class="bg-green-800 hover:bg-green-900 text-white font-bold py-2 px-4 rounded-lg">
                    <svg class="h-6 w-auto inline fill-current" 
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
                    <span class="px-2">Your podcast feed</span>
                </button>
            </a>
        </div>

        <div class="px-4">
            <div class="flex justify-center items-center">
                <a href="{{ route('channel.edit', $channel) }}">
                    <button class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2 rounded-l-lg">
                        <svg class="h-6 w-auto inline fill-current" 
                            viewBox="0 -1 401.52289 401" xmlns="http://www.w3.org/2000/svg">
                            <path d="m370.589844 250.972656c-5.523438 0-10 4.476563-10 10v88.789063c-.019532 16.5625-13.4375 29.984375-30 30h-280.589844c-16.5625-.015625-29.980469-13.4375-30-30v-260.589844c.019531-16.558594 13.4375-29.980469 30-30h88.789062c5.523438 0 10-4.476563 10-10 0-5.519531-4.476562-10-10-10h-88.789062c-27.601562.03125-49.96875 22.398437-50 50v260.59375c.03125 27.601563 22.398438 49.96875 50 50h280.589844c27.601562-.03125 49.96875-22.398437 50-50v-88.792969c0-5.523437-4.476563-10-10-10zm0 0"/>
                            <path d="m376.628906 13.441406c-17.574218-17.574218-46.066406-17.574218-63.640625 0l-178.40625 178.40625c-1.222656 1.222656-2.105469 2.738282-2.566406 4.402344l-23.460937 84.699219c-.964844 3.472656.015624 7.191406 2.5625 9.742187 2.550781 2.546875 6.269531 3.527344 9.742187 2.566406l84.699219-23.464843c1.664062-.460938 3.179687-1.34375 4.402344-2.566407l178.402343-178.410156c17.546875-17.585937 17.546875-46.054687 0-63.640625zm-220.257812 184.90625 146.011718-146.015625 47.089844 47.089844-146.015625 146.015625zm-9.40625 18.875 37.621094 37.625-52.039063 14.417969zm227.257812-142.546875-10.605468 10.605469-47.09375-47.09375 10.609374-10.605469c9.761719-9.761719 25.589844-9.761719 35.351563 0l11.738281 11.734375c9.746094 9.773438 9.746094 25.589844 0 35.359375zm0 0"/>
                        </svg> 
                        Podcast 
                    </button>
                </a>
                <a href="{{ route('channel.thumbs.edit', $channel) }}">
                    <button class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2"> 
                        <svg class="h-6 w-auto inline fill-current"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 419.2 419.2" xml:space="preserve">
                            <g>
                                <circle cx="158" cy="144.4" r="28.8"/>
                                <path d="M394.4,250.4c-13.6-12.8-30.8-21.2-49.6-23.6V80.4c0-15.6-6.4-29.6-16.4-40C318,30,304,24,288.4,24h-232
                                c-15.6,0-29.6,6.4-40,16.4C6,50.8,0,64.8,0,80.4v184.4V282v37.2c0,15.6,6.4,29.6,16.4,40c10.4,10.4,24.4,16.4,40,16.4h224.4
                                c14.8,12,33.2,19.6,53.6,19.6c23.6,0,44.8-9.6,60-24.8c15.2-15.2,24.8-36.4,24.8-60C419.2,286.8,409.6,265.6,394.4,250.4z
                                M21.2,80.4c0-9.6,4-18.4,10.4-24.8c6.4-6.4,15.2-10.4,24.8-10.4h232c9.6,0,18.4,4,24.8,10.4c6.4,6.4,10.4,15.2,10.4,24.8v124.8
                                l-59.2-58.8c-4-4-10.8-4.4-15.2,0L160,236l-60.4-60.8c-4-4-10.8-4.4-15.2,0l-63.2,64V80.4z M56,354.8v-0.4
                                c-9.6,0-18.4-4-24.8-10.4c-6-6.4-10-15.2-10-24.8V282v-12.8L92.4,198l60.4,60.4c4,4,10.8,4,15.2,0l89.2-89.6l58.4,58.8
                                c-1.2,0.4-2.4,0.8-3.6,1.2c-1.6,0.4-3.2,0.8-5.2,1.6c-1.6,0.4-3.2,1.2-4.8,1.6c-1.2,0.4-2,0.8-3.2,1.6c-1.6,0.8-2.8,1.2-4,2
                                c-2,1.2-4,2.4-6,3.6c-1.2,0.8-2,1.2-3.2,2c-0.8,0.4-1.2,0.8-2,1.2c-3.6,2.4-6.8,5.2-9.6,8.4c-15.2,15.2-24.8,36.4-24.8,60
                                c0,6,0.8,11.6,2,17.6c0.4,1.6,0.8,2.8,1.2,4.4c1.2,4,2.4,8,4,12v0.4c1.6,3.2,3.2,6.8,5.2,9.6H56z M378.8,355.2
                                c-11.6,11.6-27.2,18.4-44.8,18.4c-16.8,0-32.4-6.8-43.6-17.6c-1.6-1.6-3.2-3.6-4.8-5.2c-1.2-1.2-2.4-2.8-3.6-4
                                c-1.6-2-2.8-4.4-4-6.8c-0.8-1.6-1.6-2.8-2.4-4.4c-0.8-2-1.6-4.4-2-6.8c-0.4-1.6-1.2-3.6-1.6-5.2c-0.8-4-1.2-8.4-1.2-12.8
                                c0-17.6,7.2-33.2,18.4-44.8c11.6-11.6,27.2-18.4,44.8-18.4c17.6,0,33.2,7.2,44.8,18.4c11.6,11.2,18.4,27.2,18.4,44.8
                                C397.2,328,390,343.6,378.8,355.2z"/>
                                <path d="M368.8,299.6h-24.4v-24.4c0-6-4.8-10.8-10.8-10.8s-10.8,4.8-10.8,10.8v24.4h-24.4c-6,0-10.8,4.8-10.8,10.8
                                s4.8,10.8,10.8,10.8h24.4v24.4c0,6,4.8,10.8,10.8,10.8s10.8-4.8,10.8-10.8v-24.4h24.4c6,0,10.8-4.8,10.8-10.8
                                S374.8,299.6,368.8,299.6z"/>
                            </g>
                        </svg>
                        Cover 
                    </button>
                </a>
                <a href="{{ route('channel.medias.index', $channel) }}">
                    <button class="flex-1 bg-gray-200 shadow hover:bg-gray-400 text-gray-800 font-bold py-2 px-2 rounded-r-lg">
                        <svg class="h-6 w-auto inline fill-current"
                            xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                            viewBox="0 0 480 480" xml:space="preserve">
                            <g>
                                <path d="M415.928,88c0.019-2.111-0.798-4.144-2.272-5.656l-80-80c-1.505-1.484-3.543-2.302-5.656-2.272V0H88
                                C74.745,0,64,10.745,64,24v432c0,13.255,10.745,24,24,24h304c13.255,0,24-10.745,24-24V88H415.928z M336,27.312L388.688,80H344
                                c-4.418,0-8-3.582-8-8V27.312z M400,456c0,4.418-3.582,8-8,8H88c-4.418,0-8-3.582-8-8V24c0-4.418,3.582-8,8-8h232v56
                                c0,13.255,10.745,24,24,24h56V456z"/>
                            </g>
                            <g>
                                <path d="M144,216c-13.255,0-24,10.745-24,24s10.745,24,24,24s24-10.745,24-24S157.255,216,144,216z M144,248c-4.418,0-8-3.582-8-8
                                s3.582-8,8-8s8,3.582,8,8S148.418,248,144,248z"/>
                            </g>
                            <g>
                                <rect x="200" y="232" width="160" height="16"/>
                            </g>
                            <g>
                                <path d="M144,136c-13.255,0-24,10.745-24,24s10.745,24,24,24s24-10.745,24-24S157.255,136,144,136z M144,168c-4.418,0-8-3.582-8-8
                                s3.582-8,8-8s8,3.582,8,8S148.418,168,144,168z"/>
                            </g>
                            <g>
                                <rect x="200" y="152" width="160" height="16"/>
                            </g>
                            <g>
                                <path d="M144,296c-13.255,0-24,10.745-24,24s10.745,24,24,24s24-10.745,24-24S157.255,296,144,296z M144,328c-4.418,0-8-3.582-8-8
                                s3.582-8,8-8s8,3.582,8,8S148.418,328,144,328z"/>
                            </g>
                            <g>
                                <rect x="200" y="312" width="160" height="16"/>
                            </g>
                            <g>
                                <path d="M144,376c-13.255,0-24,10.745-24,24s10.745,24,24,24s24-10.745,24-24S157.255,376,144,376z M144,408c-4.418,0-8-3.582-8-8
                                s3.582-8,8-8s8,3.582,8,8S148.418,408,144,408z"/>
                            </g>
                            <g>
                                <rect x="200" y="392" width="160" height="16"/>
                            </g>
                        </svg>

                        Episodes 
                    </button>
                </a>
            </div>
        </div>
    </div>
    @endforeach
@else
<div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
    <p class="font-bold">You have no podcast at this time.</p>
    <p class="text-base">It's time to <a href="{{ route('channel.create') }}" class="border-b-2 border-gray-900">transform your channel into a podcast</a>.</p>
</div>
@endif