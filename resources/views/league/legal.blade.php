@extends('layouts.app')

@section('title', 'Mentions légales')

@section('content')
<x-page-header title="Mentions légales" :breadcrumbs="['Mentions légales' => null]" />

<div class="container-x max-w-3xl py-12">
    <div class="prose prose-slate max-w-none prose-headings:font-display prose-headings:uppercase prose-headings:text-navy-900">
        <h2>Éditeur du site</h2>
        <p>
            {{ setting('site_name') }} (LMVB), association loi 1901 affiliée à la Fédération Française de Volley.<br>
            Siège : {{ setting('address') }}<br>
            @if (setting('phone'))Téléphone : {{ setting('phone') }}<br>@endif
            Email : <a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a><br>
            Directrice de la publication : {{ setting('president_name') }}, présidente.
        </p>

        <h2>Hébergement</h2>
        <p>o2switch, 222-224 boulevard Gustave Flaubert, 63000 Clermont-Ferrand, France. <a href="https://www.o2switch.fr" target="_blank" rel="noopener">www.o2switch.fr</a></p>

        <h2>Résultats sportifs</h2>
        <p>Les calendriers, résultats et classements sont importés automatiquement depuis la plateforme officielle de gestion sportive de la FFVolley. En cas de divergence, seules les données officielles de la FFVolley font foi.</p>

        <h2>Crédits photos & vidéos</h2>
        <p>Logo : Ligue Martiniquaise de Volley-Ball. Photos d'illustration : <a href="https://unsplash.com" target="_blank" rel="noopener">Unsplash</a> (licence Unsplash) et Wikimedia Commons (domaine public). Vidéos d'illustration : Pexels (licence Pexels). Vidéos de matchs : chaîne YouTube de la LMVB et médias cités.</p>

        <h2>Données personnelles</h2>
        <p>
            Les informations transmises via les formulaires (contact, inscription beach, lettre d'information) sont utilisées uniquement par la ligue pour répondre à votre demande ou organiser ses événements.
            Elles ne sont ni cédées ni vendues. Conformément au RGPD, vous disposez d'un droit d'accès, de rectification et de suppression : écrivez à <a href="mailto:{{ setting('email') }}">{{ setting('email') }}</a>.
            Chaque lettre d'information contient un lien de désinscription.
        </p>

        <h2>Cookies</h2>
        <p>Le site n'utilise que des cookies techniques (session, sécurité des formulaires). Les vidéos YouTube ne sont chargées, en mode « confidentialité renforcée », qu'après un clic de votre part.</p>
    </div>
</div>
@endsection
