<!-- container for 2 cols -->
<div class="flex items-center max-w-screen-xl mx-auto px-4">
    <div class="text-center md:text-left md:w-1/2 md:w-full">
        <h1 class="text-5xl text-white tracking-normal font-extrabold leading-tight lg:text-5xl">
            Your channel <span class="block text-red-400 md:inline">in podcast</span>
        </h1>
        <div class="mt-4 text-gray-300 leading-7 md:text-2xl">
            Upload your videos on youtube (as usual).<br class="hidden md:inline">
            I'll convert it in audio and distribute your podcast to Apple Podcasts, Spotify and Google Podcasts.
        </div>
        <div class="mt-10 mx-auto text-center lg:mx-0 lg:text-left">
            <a href="#"
                class="text-white font-semibold rounded-full border-white border-2 pt-2 pb-3 px-4 sm:w-auto hover:text-gray-900 hover:bg-white focus:outline-none focus:bg-white focus:shadow-outline focus:border-gray-300">
                Sign up
            </a>
        </div>
    </div>
    <div class="hidden md:block md:w-1/2">
        @include('svg.podcast_illustration', [ 'cssClass' => 'md:w-32'])
    </div>
</div>
<!-- this one is only displayed in small to medium -->
<div class="block pt-8 lg:hidden">
    @include('svg.podcast_illustration', [ 'cssClass' => 'w-auto'])
</div>