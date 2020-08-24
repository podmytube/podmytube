@extends('layouts.www')

@section('pageTitle', "Get a podcast from a youtube business and grow your audience")

@section('content')

<main role="main" class="container" id="main">

    @include ('partials.navbar')

    <div class="container header-textpart">
        <div class="promise-text">
            Grow your <strong>Youtube business</strong> with a <strong>podcast</strong>.
        </div>

        <div class="container header-visual"></div>

        <div class="slogan text-main-color">
            The easiest way to host a podcast from a youtube business.
        </div>

        <div>
            <a class="btn btn-lg btn-success mt-5 text-uppercase" href="{{ route('register') }}" role="button">Get started free</a>
            <p class="text-muted">no credit card, cancel anytime</p>
        </div>
    </div>
</main>

<section id="features" class="container features">
    <h1>Easily transform <u>your Youtube business</u> into a glorious podcast.</h1>
    <div class="container mt-5">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-chart-line fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Grow on others platforms.</h3>
                <p>Your business is well implanted on Youtube, use the same content without effort or delay on <b>iTunes</b>, <b>Spotify</b>, <b>Soundcloud</b>, <b>Google Podcasts</b>, <b>Stitcher</b>, <b>Deezer</b> and all the audio platforms you want!</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-dollar-sign fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Find new customers.</h3>
                <p><b>Podcast</b> is the medium which is <b>gaining popularity</b> every year since its inception. By broadcasting your show as a podcast too you will <b>naturally</b> increase your <b>customer base every year</b>. Podcast is the medium to be on !</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-heart fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Podcast listeners are loyal</h3>
                <p>
                    If you plan to sell something in your show or if you only think to get a sponsor it will be a good argument.
                </p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-comment fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Enter the intimacy of your audience.</h3>
                <p>With a podcast, your <b>voice comes without filter</b> directly <b>into the ears</b> of your listeners. All will tell you, listening to a podcast <b>without all the distractions of a screen</b>, <b>creates</b> a very <b>strong link</b> between the host and his audience. Podmytube will help you build this link!</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-cogs fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Focus on what you really like.</h3>
                <p>You don't want to hear about technique and you are right ! With Podmytube your <b>podcast will be updated</b> as soon as <b>you upload a video on Youtube</b>, without you <b>doing anything</b> and the sound quality of your show will be respected.</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-gift fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Podcast is simple, modern and addictive.</h3>
                <p>For your fans too, simplicity is also essential. Once subscribed to your podcast on the device of their choice, each of your episode will be downloaded, added to its playlist and he will only have to press Play to listen to you.</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-filter fa-3x text-main-color" aria-hidden="true"></i>
                <h3>You decide which episode is getting in or not.</h3>
                <p>Podmytube is <b>the only service</b> that allows you to fully control the content of your podcast. One unsuitable episode ? A <b>tag filtration</b> system (direct from Youtube) allows you to avoid including it in your podcast. You want to host your podcast on your .com, it's possible without any additional cost.</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fab fa-youtube fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Distinguish yourself.</h3>
                <p>Every minute <b>400 hours of videos are uploaded on Youtube</b>. This on <b>more than 50 million channels</b>. Meanwhile Apple confirmed to have <b>500,000 active podcasts</b> registered on iTunes. It's much, certainly, but much less !!! Take your place now !</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-hdd fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Hosted.</h3>
                <p>You can <b>smile</b>, it's impossible to be more simple. Once created, your podcast will then be hosted on PodMyTube's servers. <b>No bandwidth cost</b> ! <b>No hosting service fee</b> ! Everything is included. </p>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-smile fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Happy customers</h3>
                <p>{{ \App\User::all()->count() }}</p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-file-audio fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Audio files generated</h3>
                <p>
                    {{ \App\Media::grabbedAt()->count() }} files<br>
                    250 Go storage
                </p>
            </div>
            <div class="col-xs-12 col-sm-6 col-lg-4 featbox">
                <i class="fas fa-cloud-download-alt fa-3x text-main-color" aria-hidden="true"></i>
                <h3>Downloads/Day</h3>
                <p>10000+ episodes are downloaded from our servers each day.</p>
            </div>
        </div>
        <!--/row-->
    </div>
    <!--/container-->
</section>
<!-- /features -->

<section id="feedback" class="feedback-section bg-main-color">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="text-white">
                    Their feedback
                </h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="bubble">
                    "Podmytube is part of the weapons of new web entrepreneurs, it avoids having to make additional manipulations because it is 100% automatic!"
                </div>
                <div class="comments-avatar">
                    <a href="" class="pull-left ">
                        <img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="/images/cyro-torres.jpg" alt="" />
                        <div>
                            <div class="comments-name">Cyro</div>
                            <div class="comments-channel">
                                <a href="https://www.youtube.com/channel/UCBXJGoueIDn_uHpvMWv_cRQ">Music your life</a>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bubble">
                    "Podmytube is an excellent service and of an irreproachable quality. Fred provides a regular feedback on the positioning and evolution of the podcast. Go there with your eyes closed!"
                </div>
                <div class="comments-avatar">
                    <a href="" class="pull-left">
                        <img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="/images/jean-viet.jpg" alt="" />
                        <div>
                            <div class="comments-name">Jean Viet</div>
                            <div class="comments-channel">
                                <a href="https://www.youtube.com/channel/UCu0tUATmSnMMCbCRRYXmVlQ">Jean Viet</a>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bubble">
                    "Frédérick offers an automatic, quality, innovative and cheap podcast creation service. I do not even have to deal with this thing, it works by itself with every new YouTube video published."
                </div>
                <div class="comments-avatar">
                    <a href="" class="pull-left">
                        <img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="/images/la-pause-tech.jpg" alt="" />
                        <div>
                            <div class="comments-name">Vito</div>
                            <div class="comments-channel">
                                <a href="https://www.youtube.com/channel/UCeQRtQb7yg3tanWAS0cSYJw">La pause tech</a>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</section>


@include ('partials.www.pricing')

<section id="about" class="about-section">
    <div class="container bg-main-color text-white p-3">
        <div class="row">
            <div class="col-md-8">
                <h3 class="text-white">About Podmytube</h3>
                <p>
                    <strong>Podmytube was founded during christmas 2016 after I discovered that Youtube was hosting great shows that I would have loved to find in podcast format.</strong>
                </p>
                <p>
                    My goal is to give Youtube hosts the opportunity to create a podcast just from the videos of their youtube channel. The whole process has been designed to make podcast management as simple as possible. So that you can focus on what you do the best "produce content"
                </p>
                <p>
                    Don't wait,
                    <strong>
                        <a href="{{ env('DASH_URL')}}" class="signin_free">try Podmytube for free</a>
                    </strong>
                    !
                </p>
            </div>
            <div class="col-md-4 text-center">
                <img class="img-fluid img-profile rounded-circle mx-auto mb-2" src="images/small-fred-2020.jpg" alt="Frederick Tyteca" />
                <h4 class="text-white">Frédérick Tyteca</h4>
            </div>
        </div>
    </div>
</section>

<section class="post_footer">
    <div class="container text-center">
        <ul class="list-inline">
            <li class="list-inline-item"><a href="https://twitter.com/podmytube" class="nav-link" target="tab"> <i class="fab fa-twitter fa-2x text-main-color"></i> </a></li>
            <li class="list-inline-item"><a href="https://www.facebook.com/Podmytube" class="nav-link" target="tab"> <i class="fab fa-facebook fa-2x text-main-color"></i> </a></li>
        </ul>
    </div>
</section>
@endsection