@extends('layouts.admin')

@section('titulo', 'Inicio')

@section('conteudo')
    <h1 class="painel__titulo">Ola, {{ auth()->user()?->name }}</h1>
    <p class="painel__subtitulo">
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    {{-- Os numeros vem contados do banco (COUNT), nao carregando as listas
         inteiras para contar aqui. Ver PainelController. --}}
    <div class="numeros">
        <div class="numero">
            <div class="numero__valor">{{ $totalProjetos }}</div>
            <div class="numero__rotulo">Projetos publicados</div>
        </div>

        <div class="numero">
            <div class="numero__valor">{{ $totalHabilidades }}</div>
            <div class="numero__rotulo">Habilidades cadastradas</div>
        </div>

        <div class="numero">
            <div class="numero__valor">{{ $totalSecoesVisiveis }}</div>
            <div class="numero__rotulo">Secoes ligadas no site</div>
        </div>
    </div>
@endsection