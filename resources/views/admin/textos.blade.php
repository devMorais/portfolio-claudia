@extends('layouts.admin')

@section('titulo', 'Textos')

@section('conteudo')
    <h1 class="painel__titulo">Textos</h1>
    <p class="painel__subtitulo">
        Edite a apresentação do topo e a história em 3 atos.
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    @include('admin._textos-form')
@endsection