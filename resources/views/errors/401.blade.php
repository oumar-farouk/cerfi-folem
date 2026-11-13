{{-- resources/views/errors/403.blade.php
     Le message de l'exception est volontairement ignoré : Spatie renvoie un texte
     technique en anglais ("User does not have the right roles"). --}}
@extends('errors.layout')

@section('code', '401')
@section('titre', 'Accès refusé')
@section('message', "Votre profil n'a pas accès à cette section. Si vous pensez qu'il s'agit d'une erreur, contactez l'administrateur de la plateforme.")
