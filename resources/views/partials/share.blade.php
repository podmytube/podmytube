
<ul class="flex items-center text-white">
    <li>
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($title) }}&url={{ $url }}">
            <svg class="h-8 w-8 mx-1 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36">
                <path fill-rule="evenodd"
                    d="M12.43 30.38c11.33 0 17.52-9.38 17.52-17.51 0-.27 0-.53-.02-.8 1.2-.87 2.25-1.95 3.07-3.18-1.1.49-2.29.82-3.53.97a6.18 6.18 0 0 0 2.7-3.4c-1.19.7-2.5 1.2-3.9 1.48a6.15 6.15 0 0 0-10.5 5.62A17.47 17.47 0 0 1 5.1 7.13a6.13 6.13 0 0 0 1.9 8.21 6.1 6.1 0 0 1-2.78-.77v.08a6.16 6.16 0 0 0 4.93 6.04 6.17 6.17 0 0 1-2.78.1 6.16 6.16 0 0 0 5.75 4.28A12.35 12.35 0 0 1 3 27.62a17.43 17.43 0 0 0 9.43 2.76">
                </path>
            </svg>
        </a>
    </li>
    <li>
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}&t={{ urlencode($title) }}">
            <svg class="h-8 w-8 mx-1 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36">
                <path fill-rule="evenodd"
                    d="M32 18a14 14 0 1 0-16.19 13.83v-9.78h-3.55V18h3.55v-3.08c0-3.51 2.1-5.45 5.3-5.45 1.52 0 3.12.27 3.12.27v3.45h-1.76c-1.74 0-2.28 1.08-2.28 2.18V18h3.88l-.62 4.05h-3.26v9.78A14 14 0 0 0 32 18">
                </path>
            </svg>
        </a>
    </li>
    <li>
        <a href="https://www.linkedin.com/shareArticle?url={{ urlencode($url) }}&title={{ urlencode($title) }}&mini=true">
            <svg class="h-7 w-7 mx-1 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                <path fill-rule="evenodd" d="m256 0c-141.363281 0-256 114.636719-256 256s114.636719 256 256 256 256-114.636719 256-256-114.636719-256-256-256zm-74.390625 387h-62.347656v-187.574219h62.347656zm-31.171875-213.1875h-.40625c-20.921875 0-34.453125-14.402344-34.453125-32.402344 0-18.40625 13.945313-32.410156 35.273437-32.410156 21.328126 0 34.453126 14.003906 34.859376 32.410156 0 18-13.53125 32.402344-35.273438 32.402344zm255.984375 213.1875h-62.339844v-100.347656c0-25.21875-9.027343-42.417969-31.585937-42.417969-17.222656 0-27.480469 11.601563-31.988282 22.800781-1.648437 4.007813-2.050781 9.609375-2.050781 15.214844v104.75h-62.34375s.816407-169.976562 0-187.574219h62.34375v26.558594c8.285157-12.78125 23.109375-30.960937 56.1875-30.960937 41.019531 0 71.777344 26.808593 71.777344 84.421874zm0 0"/>
            </svg>
        </a>
    </li>
</ul>








<ul class="text-white">
    <li class="list-inline-item">
        <a href="https://twitter.com/intent/tweet?text={{ urlencode($title) }}&url={{ $url }}">
            <span class="fa-stack fa-lg">
                <i class="fas fa-circle fa-stack-2x"></i>
                <i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
            </span>
        </a>
    </li>
    <li class="list-inline-item">
        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($url) }}&t={{ urlencode($title) }}">
            <span class="fa-stack fa-lg">
                <i class="fas fa-circle fa-stack-2x"></i>
                <i class="fab fa-facebook-f fa-stack-1x fa-inverse"></i>
            </span>
        </a>
    </li>
    <li class="list-inline-item">
        <a href="https://www.linkedin.com/shareArticle?url={{ urlencode($url) }}&title={{ urlencode($title) }}&mini=true">
            <span class="fa-stack fa-lg">
                <i class="fas fa-circle fa-stack-2x"></i>
                <i class="fab fa-linkedin fa-stack-1x fa-inverse"></i>
            </span>
        </a>
    </li>
</ul>
