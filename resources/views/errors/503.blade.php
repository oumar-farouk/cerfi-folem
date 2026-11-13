{{-- resources/views/errors/503.blade.php
     Mode maintenance. Pour que cette page s'affiche même si l'application ne
     démarre plus, pré-rendez-la au moment de la mise en maintenance :
     php artisan down --render="errors.503" --retry=60 --secret="un-mot-secret" --}}
@extends('errors.layout')

@section('code', '503')
@section('titre', 'Maintenance en cours')
@section('message', "FOLEM est momentanément indisponible pour une opération de maintenance. Nous revenons très vite : merci de votre patience.")

@section('actions')
    <button type="button" class="btn btn-primaire" onclick="location.reload()">Actualiser</button>
@endsection
