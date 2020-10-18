@extends('layouts.app')

@section('pageTitle', 'Terms of service')

@section('content')

<div class="mx-auto">
    <div class="container pb-6 md:pb-12 mx-auto text-white">
        <div class="text-center mt-12 mb-8">
            <h3 class="text-3xl md:text-5xl text-white font-semibold">Terms of service</h3>
        </div>

    <p>Use of the Podmytube web site and all associated services is offered subject to agreement to the following terms of service.</p>

    <h3>Welcome to Podmytube</h3>
    <p>Podmytube offer service that enable Youtube Creator to host any podcast.
        These podcast are generated from their Youtube channel(s) content with the author agreement.</p>

    <h4>Free account</h4>
    <p>Podmytube is providing a free acount plan. This plan is subject to any modification at any time and for any reason (or no reason).</p>

    <h4>Abuse</h4>
    <p>
        Podmytube reserve the right to take action in response to reported abuse of our services.
        This action may include - but is not limited to - the deletion of hosted podcasts or other account details.
        Abuse include (of course) any podcast registered without the agreement of the youtube channel owner. It includes too - but is not limited to - any action which is illegal under the city, state, or federal laws where you are currently present.
        This includes copyright infringement under the DMCA or any activity which we deem disruptive.
    </p>


    <h4>Refund policy</h4>
    <p>If you are unhappy with the service, Podmytube will offer a full refund of the last month subscription. Please <a href="mailto:contact@podmytube.com"><i class="fas fa-envelope-square"></i> Contact me </a> in such a case.</p>

    <h4>No Warranty</h4>
    <p>All services are provided "as is" without any warranty of any kind including but not limited to fitness for a particular purpose.</p>

    <h4>Modification of Agreement</h4>
    <p>Podmytube reserves the right to modify this agreement at any time without prior notice.</p>

    <h4>Privacy</h4>
    <p>Podmytube have <a href="{{ route('privacy') }}">a complete and extensive Privacy Policy</a>. Please refer to the separate document.</p>

    <span class="text-sm">Last updated on october 19, 2020</span>

</div>

@endsection