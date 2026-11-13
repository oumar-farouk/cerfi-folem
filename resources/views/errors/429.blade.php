{{-- resources/views/errors/429.blade.php
     Affiche le délai d'attente quand Laravel fournit l'en-tête Retry-After
     (cas des routes limitées par throttle : paiement, récépissé, pointage…). --}}
@php
    $attente = isset($exception) ? ($exception->getHeaders()['Retry-After'] ?? null) : null;
@endphp
@extends('errors.layout')

@section('code', '429')
@section('titre', 'Trop de tentatives')
@section('message')
    Vous avez effectué trop de requêtes en peu de temps.
    @if (is_numeric($attente))
        Patientez environ {{ $attente }} seconde(s) avant de réessayer.
    @else
        Patientez quelques instants avant de réessayer.
    @endif
@endsection

@section('actions')
    <button type="button" class="btn btn-primaire" onclick="location.reload()">Réessayer</button>
    <a href="{{ url('/') }}" class="btn btn-secondaire">Accueil</a>
@endsection
