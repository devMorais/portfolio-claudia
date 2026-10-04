@extends('layouts.admin')

@section('titulo', 'Habilidades')

@section('conteudo')
    <h1 class="painel__titulo">Habilidades</h1>
    <p class="painel__subtitulo">
        Adicione, edite, exclua e reordene com as setas. A ordem daqui é a ordem do site.
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    @if (session('sucesso'))
        <div class="aviso aviso--sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    <section class="bloco">
        <div class="bloco__topo">
            <h2 class="bloco__titulo">Habilidades técnicas ({{ $tecnicas->count() }})</h2>
            <a href="{{ route('admin.habilidades.create', ['tipo' => 'tecnica']) }}" class="painel__botao">Adicionar técnica</a>
        </div>

        @include('admin.habilidades._lista', [
            'itens' => $tecnicas,
            'mensagemVazia' => 'Nenhuma habilidade técnica ainda.',
        ])
    </section>

    <section class="bloco">
        <div class="bloco__topo">
            <h2 class="bloco__titulo">Habilidades humanas ({{ $humanas->count() }})</h2>
            <a href="{{ route('admin.habilidades.create', ['tipo' => 'humana']) }}" class="painel__botao">Adicionar humana</a>
        </div>

        @include('admin.habilidades._lista', [
            'itens' => $humanas,
            'mensagemVazia' => 'Nenhuma habilidade humana ainda.',
        ])
    </section>
@endsection