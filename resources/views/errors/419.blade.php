{{-- resources/views/errors/419.blade.php --}}
@extends('errors.layout')

@section('code', '419')
@section('titre', 'Page expirée')
@section('message', "Votre session a expiré ou le formulaire est resté ouvert trop longtemps. Revenez à la page précédente, rechargez-la (touche F5), puis recommencez.")

@section('actions')
    <button type="button" class="btn btn-primaire" onclick="history.back()">Revenir à la page précédente</button>
    <a href="{{ url('/') }}" class="btn btn-secondaire">Accueil</a>
@endsection
