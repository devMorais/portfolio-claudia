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

    {{-- Esta caixa sai do painel quando as telas de edicao estiverem prontas. --}}
    <div class="a-fazer">
        <h2 class="bloco__titulo">O que falta construir aqui</h2>
        <p class="painel__subtitulo" style="margin-bottom: 0;">
            A estrutura do painel (login, layout, menu, esta tela) esta pronta.
            Cada item abaixo e uma card no board Portfolio Claudia, no Avante.
        </p>

        <ul class="a-fazer__lista">
            <li><strong>PC-15</strong> — Habilidades e trajetoria: criar, editar, remover e reordenar</li>
            <li><strong>PC-16</strong> — Projetos: CRUD com envio de captura de tela</li>
            <li><strong>PC-17</strong> — Configuracoes: redes, curriculo, SEO e liga/desliga de secoes</li>
        </ul>
    </div>
@endsection