<div class="flex items-center max-w-screen-xl mx-auto px-4">
    <div class="flex md:py-4">
        <div class="text-center md:flex-shrink-0 md:flex-grow-0 md:w-1/2 md:text-left md:pt-8">
            <h1 class="text-5xl text-white tracking-normal font-extrabold leading-tighter md:text-5xl">
                Your channel <span class="block text-red-400 lg:inline">in podcast</span>
            </h1>
            <p class="mt-4 text-gray-300 leading-7 md:text-lg">
                Upload your videos on youtube <strong>(as usual)</strong>.
            </p>
            <p class="mt-4 text-gray-300 leading-7 md:text-lg">
                I'll convert it in audio and distribute your podcast to
                Apple Podcasts, Spotify and Google Podcasts.
            </p>
            <div class="mt-6 mx-auto text-center lg:mx-0 lg:text-left">
                <a href="#"
                    class="text-white font-semibold rounded-full border-white border-2 pt-2 pb-3 px-4 sm:w-auto hover:text-gray-900 hover:bg-white focus:outline-none focus:bg-white focus:shadow-outline focus:border-gray-300">
                    Sign up
                </a>
            </div>
        </div>
        <div class="hidden md:block md:flex-shrink-0 md:flex-grow-0 md:w-1/2">
            @include('svg.podcast_illustration', [ 'cssClass' => 'h-full w-full'])
        </div>
    </div>
</div>
<!-- this one is only displayed in tiny -->
<div class="block pt-12 md:hidden">
    @include('svg.podcast_illustration', [ 'cssClass' => 'h-48 mx-auto w-auto sm:h-64'])
</div>