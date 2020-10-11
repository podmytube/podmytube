<nav class="flex items-center justify-between flex-wrap bg-gray-300 p-6">
    <div class="flex items-center flex-shrink-0 mr-6">
        <img src="/images/logo-small.png" class="h-12">
    </div>
    <div class="block lg:hidden">
        <button class="flex items-center px-3 py-2 border rounded hover:text-black hover:border-black" id="mainMenu">
            <svg class="fill-current h-3 w-3" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <title>Menu</title>
                <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" />
            </svg>
        </button>
    </div>
    <div class="hidden w-full lg:flex-grow lg:block lg:flex lg:items-center lg:w-auto" id="mainMenuLinks">
        <div class="text-sm lg:flex-grow border-gray-400 border-b-2">
            <a class="block mt-4 lg:inline-block lg:mt-0 text-gray-900 hover:border-red-900 active:border-red-900 mr-4 pb-4"
                href="{{ route('post.index') }}">
                Blog
            </a>
            <a class="block mt-4 lg:inline-block lg:mt-0 text-gray-900 hover:border-red-900 active:border-red-900 border-b-2 mr-4"
                href="#responsive-header">
                Features
            </a>
            <a href="#responsive-header" class="block mt-4 lg:inline-block lg:mt-0 text-gray-900 hover:text-white mr-4">
                Pricing
            </a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        document.getElementById("mainMenu").addEventListener("click", function(event){
            var menuLinks = document.getElementById('mainMenuLinks');
            if (menuLinks.style.display == "" || menuLinks.style.display == "none") {
                menuLinks.style.display = "block";
            } else {
                menuLinks.style.display = "none";
            }
        });        
    });
</script>