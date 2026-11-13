{{-- resources/views/errors/404.blade.php --}}
@extends('errors.layout')

@section('code', '404')
@section('titre', 'Page introuvable')
@section('message', "La page que vous cherchez n'existe pas ou a été déplacée. Vérifiez l'adresse saisie, ou revenez à l'accueil.")
