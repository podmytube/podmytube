@extends('layouts.app')

@section('pageTitle', 'Terms of service')

@section('content')

<div class="container mt-4">

    <div class="alert alert-secondary">
        <i class="fas fa-info-circle"></i>
        For your convenience, <a href="#version-francaise">a french translation of this page</a> is provided below. This translation is for informational purposes only, and the definitive version of this page is the English version.
    </div>

    <h3>Terms of service</h3>
    <span class="text-muted">Last updated on march 7, 2020</span>
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
</div>

<hr style="margin:4rem 0">

<div class="container" style="margin-top:1.5rem;font-size: 14px;font-weight:300;" id="version-francaise">
    <h3 class="text-dark"> Conditions d'utilisation </h3>
    <p> L'utilisation du site web de Podmytube et de tous les services associés est offerte sous réserve de l'accord avec les conditions de service suivantes. </p>

    <h3 class="text-dark"> Bienvenue à Podmytube </h3>
    <p> Podmytube offre un service qui permet à tout propriétaire de chaine Youtube de produire un podcast. Ces podcasts sont générés à partir du contenu de leur chaîne Youtube. </p>

    <h4 class="text-dark"> Compte gratuit </h4>
    <p> Podmytube propose une version gratuite. Ce tarif est susceptible d'être modifié à tout moment et pour quelque raison que ce soit (ou sans raison). </p>

    <h4 class="text-dark"> Abus </h4>
    <p>
        Podmytube se réserve le droit de prendre des mesures en réponse à un abus signalé de nos services.
        Cette action peut inclure, sans s'y limiter, la suppression des podcasts hébergés ou d'autres détails de compte.
        Les abus incluent (bien sûr) tout podcast enregistré sans l'accord du propriétaire de la chaîne youtube. 
        Cela inclut également - mais sans s'y limiter - à toute action illégale en vertu des lois de la ville, de l'État ou du gouvernement fédéral où vous êtes actuellement présent.
        Cela inclut la violation des droits d'auteur en vertu du DMCA ou toute activité que nous jugeons perturbatrice.
    </p>

    <h4 class="text-dark"> Politique de remboursement </h4>
    <p> 
        Si vous n'êtes pas satisfait du service, Podmytube vous offrira un remboursement complet de l'abonnement du mois dernier. 
        Veuillez <a href="mailto:contact@podmytube.com"> <i class="fas fa-enveloppe-square"> </i> me contacter </a> dans un tel cas. 
    </p>

    <h4 class="text-dark"> Aucune garantie </h4>
    <p> Tous les services sont fournis "en l'état" sans aucune garantie d'aucune sorte, y compris mais sans s'y limiter, l'adéquation à un usage particulier. </p>

    <h4 class="text-dark"> Modification de l'accord </h4>
    <p> Podmytube se réserve le droit de modifier cet accord à tout moment et sans préavis. </p>

    <h4> Confidentialité </h4>
    <p> Podmytube a <a href="{{ route('privacy') }}">une politique de confidentialité complète</a>. Veuillez vous référer au document séparé. </p>
</div>

@endsection