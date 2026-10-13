@extends('layouts.admin')

@section('titulo', 'Trajetória')

@section('conteudo')
    <h1 class="painel__titulo">Trajetória</h1>
    <p class="painel__subtitulo">
        A linha do tempo da carreira. Adicione, edite, exclua e reordene com as setas.
        A ordem daqui é a ordem do site.
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    @if (session('sucesso'))
        <div class="aviso aviso--sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    <section class="bloco">
        <div class="bloco__topo">
            <h2 class="bloco__titulo">Linha do tempo ({{ $itens->count() }})</h2>
            <a href="{{ route('admin.trajetorias.create') }}" class="painel__botao">Adicionar item</a>
        </div>

        @if ($itens->isEmpty())
            <p class="vazio">Nenhum item na linha do tempo ainda.</p>
        @else
            <ol class="habilidades-admin">
                @foreach ($itens as $item)
                    <li class="habilidade-admin" id="t-{{ $item->id }}">
                        <div class="habilidade-admin__info">
                            <span class="habilidade-admin__nome">{{ $item->titulo }}</span>
                            <span class="habilidade-admin__meta">
                                {{ $item->periodo }} &middot; {{ $areas[$item->area] ?? $item->area }}
                                @if ($item->subtitulo) &middot; {{ $item->subtitulo }} @endif
                            </span>
                        </div>

                        <div class="habilidade-admin__acoes">
                            <form method="POST" action="{{ route('admin.trajetorias.subir', $item) }}">
                                @csrf
                                <button type="submit" class="botao-mini" title="Subir"
                                        aria-label="Subir {{ $item->titulo }}" @disabled($loop->first)>&uarr;</button>
                            </form>

                            <form method="POST" action="{{ route('admin.trajetorias.descer', $item) }}">
                                @csrf
                                <button type="submit" class="botao-mini" title="Descer"
                                        aria-label="Descer {{ $item->titulo }}" @disabled($loop->last)>&darr;</button>
                            </form>

                            <a href="{{ route('admin.trajetorias.edit', $item) }}" class="botao-mini">Editar</a>

                            <form method="POST" action="{{ route('admin.trajetorias.destroy', $item) }}"
                                  data-confirma="Excluir o item &quot;{{ $item->titulo }}&quot;? Isso não pode ser desfeito."
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