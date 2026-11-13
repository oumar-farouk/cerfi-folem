{{-- resources/views/errors/500.blade.php
     Aucun détail technique n'est affiché : ils restent dans storage/logs. --}}
@extends('errors.layout')

@section('code', '500')
@section('titre', 'Une erreur est survenue')
@section('message', "Un problème est survenu de notre côté. Réessayez dans quelques instants. Si l'erreur persiste, contactez l'organisation du forum.")

@section('actions')
    <button type="button" class="btn btn-primaire" onclick="location.reload()">Réessayer</button>
    <a href="{{ url('/') }}" class="btn btn-secondaire">Accueil</a>
@endsection
