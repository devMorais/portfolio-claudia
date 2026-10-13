@extends('layouts.admin')

@section('titulo', 'Projetos')

@section('conteudo')
    <h1 class="painel__titulo">Projetos</h1>
    <p class="painel__subtitulo">
        Adicione, edite, exclua e reordene com as setas. A ordem daqui é a ordem do site.
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    @if (session('sucesso'))
        <div class="aviso aviso--sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    <section class="bloco">
        <div class="bloco__topo">
            <h2 class="bloco__titulo">Projetos ({{ $itens->count() }})</h2>
            <a href="{{ route('admin.projetos.create') }}" class="painel__botao">Adicionar projeto</a>
        </div>

        @if ($itens->isEmpty())
            <p class="vazio">Nenhum projeto ainda.</p>
        @else
            <ol class="habilidades-admin">
                @foreach ($itens as $item)
                    <li class="habilidade-admin" id="p-{{ $item->id }}">
                        <div class="habilidade-admin__info">
                            <span class="habilidade-admin__nome">{{ $item->nome }}</span>
                            <span class="habilidade-admin__meta">
                                {{ $item->papel ?: 'sem papel' }}
                                &middot; {{ $item->imagem_url ? 'com imagem' : 'sem imagem' }}
                                &middot; {{ $item->repositorio_url ? 'com repositório' : 'sem repositório' }}
                            </span>
                        </div>

                        <div class="habilidade-admin__acoes">
                            <form method="POST" action="{{ route('admin.projetos.subir', $item) }}">
                                @csrf
                                <button type="submit" class="botao-mini" title="Subir"
                                        aria-label="Subir {{ $item->nome }}" @disabled($loop->first)>&uarr;</button>
                            </form>

                            <form method="POST" action="{{ route('admin.projetos.descer', $item) }}">
                                @csrf
                                <button type="submit" class="botao-mini" title="Descer"
                                        aria-label="Descer {{ $item->nome }}" @disabled($loop->last)>&darr;</button>
                            </form>

                            <a href="{{ route('admin.projetos.edit', $item) }}" class="botao-mini">Editar</a>

                            <form method="POST" action="{{ route('admin.projetos.destroy', $item) }}"
                                  data-confirma="Excluir o projeto &quot;{{ $item->nome }}&quot;? A imagem dele também será apagada. Isso não pode ser desfeito."
                                  onsubmit="return confirm(this.dataset.confirma)">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="botao-mini botao-mini--perigo">Excluir</button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </section>
@endsection