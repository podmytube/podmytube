<header class="text-gray-100 sm:flex sm:justify-between sm:py-2 sm:px-4 max-w-screen-xl mx-auto">
    <div class="flex items-center justify-between py-2 sm:p-0">
        <div>
            @guest
            <a href="{{ route('www.index') }}">
            @else
            <a href="{{ route('home') }}">
            @endguest
                <img class="h-12" src="/images/podmytube-logo-2020-150x53.png">
            </a>
        </div>
        <div class="pr-2 sm:hidden">
            <button id="mobile-nav-trigger" class="block text-gray-400 hover:text-white focus:text-white focus:outline-none">
                <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <title>Menu</title>
                    <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"></path>
                </svg>
            </button>
        </div>
    </div>
    <div class="hidden pt-2 pb-4 text-center sm:flex sm:items-center sm:p-0" id="mobile-nav">
    @guest
        <a class="     block px-2 py-1 text-gray-100 rounded hover:bg-gray-800" href="{{ route('pricing') }}"> Pricing </a>
        <a class="mt-1 block px-2 py-1 text-gray-100 rounded hover:bg-gray-800 sm:mt-0 sm:mr-2" href="{{ route('post.index') }}"> Blog </a>
        <a class="mt-1 block px-2 py-1 text-gray-100 rounded hover:bg-gray-800 sm:mt-0 sm:mr-2" href="{{ route('about') }}"> About </a>
        <a class="mt-1 block px-2 py-1 text-gray-100 rounded hover:bg-gray-800 sm:mt-0 sm:mr-2" href="{{ route('login') }}"> Log In </a>
        <a class="mt-1 block px-2 py-1 text-gray-100 rounded hover:bg-gray-800 sm:mt-0 sm:mr-2 sm:hover:bg-white sm:border sm:rounded sm:border-white hover:border-transparent hover:text-gray-900" href="{{ route('register') }}"> Sign Up </a>
    @else
        <a class="     block px-2 py-1 text-gray-100 rounded hover:bg-gray-800" href="{{ route('home') }}"> Dashboard </a>
        <a class="mt-1 block px-2 py-1 text-gray-100 rounded hover:bg-gray-800 sm:mt-0 sm:mr-2" href="{{ route('channel.create') }}"> Add your podcast </a>
        <a class="mt-1 block px-2 py-1 text-gray-100 rounded hover:bg-gray-800 sm:mt-0 sm:mr-2" href="#" id="logout-link"> Logout </a>
    @endguest
    </div>
</header>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        var navIsOpen=false;

        var logoutLink=document.getElementById("logout-link");
        if(logoutLink){
            logoutLink.addEventListener("click", function(event){
                document.getElementById('logout-form').submit();
            });
        }
        
        document.getElementById("mobile-nav-trigger").addEventListener("click", function(event){
            toggleNav(document.getElementById("mobile-nav"));
        });

        function toggleNav(element){
            if (navIsOpen){
                // hide it
                removeClass(element, "block");
                addClass(element, "hidden");
                navIsOpen=false;
                return true;
            }
            // show it
            removeClass(element, "hidden");
            addClass(element, "block");
            navIsOpen=true;
            return true;
        }

        function hasClass(element, className) {
            return !!element.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'));
        }

        function addClass(element, className) {
            if (!hasClass(element, className)) element.className += " " + className;
        }

        function removeClass(element, className) {
            if (hasClass(element, className)) {
                var reg = new RegExp('(\\s|^)' + className + '(\\s|$)');
                element.className = element.className.replace(reg, ' ');
            }
        }
    });
</script>