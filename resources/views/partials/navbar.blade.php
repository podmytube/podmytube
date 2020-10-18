<header class="container mx-auto sticky top-0 z-20 bg-gray-900" style="opacity:0.95;">
    <nav id="nav" class="flex items-center justify-between flex-wrap sm:pt-6 sm:pb-6 md:pt-6 md:pb-6 lg:pt-10 lg:pb-10">
        <div class="flex items-center flex-shrink-0 text-white mr-8">
            <a href="/"><img src="/images/podmytube-logo-2020-150x53.png"></a>
        </div>
        <div class="block md:hidden">
            <button id="mobile-nav-trigger"
                class="flex items-center px-3 py-2 border rounded text-white border-black-400 hover:text-white hover:border-white">
                <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <title>Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
                </svg>
            </button>
        </div>
        <div id="mobile-nav" class="hidden text-center w-full flex-grow md:text-left md:pt-1 md:flex md:items-center md:w-auto">
            <div class="text-sm md:flex-grow">
                <a href="/pricing"
                    class="block mt-4 md:inline-block md:mt-0 text-white md:border-b-2 md:border-transparent md:hover:border-white md:hover:border-b-2 mr-0 md:mr-4">
                    Pricing
                </a>
                <a href="{{ route('post.index') }}"
                    class="block mt-4 md:inline-block md:mt-0 text-white md:border-b-2 md:border-transparent md:hover:border-white md:hover:border-b-2 mr-0 md:mr-4">
                    Blog
                </a>
            </div>
            @guest
            <div id="login-signup" class="">
                <a href="{{ route('login') }}"
                    class="block mt-4 md:inline-block md:mt-0 text-sm text-white md:border-b-2 md:border-transparent md:hover:border-white md:hover:border-b-2 mr-0 md:mr-4">
                    Log In
                </a>
                <a href="{{ route('register') }}"
                    class="inline-block text-sm px-4 py-2 leading-none border rounded text-white border-white hover:border-transparent hover:text-gray-900 hover:bg-white mt-4 md:mt-0">Sign
                    Up</a>
            </div>
            @else
            <div id="signed-in">
                <a href="{{ route('root') }}"
                    class="block mt-4 md:inline-block md:mt-0 text-sm font-semibold text-white md:border-b-2 md:border-transparent md:hover:border-white md:hover:border-b-2 mr-4">
                    Your Dashboard →
                </a>
            </div>
            @endguest
        </div>
    </nav>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            document.getElementById("mobile-nav-trigger").addEventListener("click", function(event){
                var menuLinks = document.getElementById('mobile-nav');
                if (menuLinks.style.display == "" || menuLinks.style.display == "none") {
                    menuLinks.style.display = "block";
                } else {
                    menuLinks.style.display = "none";
                }
            });        
        });
    </script>
</header>