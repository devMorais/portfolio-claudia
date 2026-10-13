@extends('layouts.admin')

@section('titulo', 'Formações')

@section('conteudo')
    <h1 class="painel__titulo">Formações</h1>
    <p class="painel__subtitulo">
        Cursos e certificados. Adicione, edite, exclua e reordene com as setas.
        A ordem daqui é a ordem do site.
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    @if (session('sucesso'))
        <div class="aviso aviso--sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    <section class="bloco">
        <div class="bloco__topo">
            <h2 class="bloco__titulo">Formações ({{ $itens->count() }})</h2>
            <a href="{{ route('admin.formacoes.create') }}" class="painel__botao">Adicionar formação</a>
        </div>

        @if ($itens->isEmpty())
            <p class="vazio">Nenhuma formação ainda.</p>
        @else
            <ol class="habilidades-admin">
                @foreach ($itens as $item)
                    <li class="habilidade-admin" id="f-{{ $item->id }}">
                        <div class="habilidade-admin__info">
                            <span class="habilidade-admin__nome">{{ $item->titulo }}</span>
                            <span class="habilidade-admin__meta">
                                {{ collect([$item->instituicao, $item->ano, $item->carga_horaria])->filter()->join(' · ') ?: 'sem detalhes' }}
                                &middot; {{ $item->certificado_url ? 'com certificado' : 'sem certificado' }}
                            </span>
                        </div>

                        <div class="habilidade-admin__acoes">
                            <form method="POST" action="{{ route('admin.formacoes.subir', $item) }}">
                                @csrf
                                <button type="submit" class="botao-mini" title="Subir"
                                        aria-label="Subir {{ $item->titulo }}" @disabled($loop->first)>&uarr;</button>
                            </form>

                            <form method="POST" action="{{ route('admin.formacoes.descer', $item) }}">
                                @csrf
                                <button type="submit" class="botao-mini" title="Descer"
                                        aria-label="Descer {{ $item->titulo }}" @disabled($loop->last)>&darr;</button>
                            </form>

                            <a href="{{ route('admin.formacoes.edit', $item) }}" class="botao-mini">Editar</a>

                            <form method="POST" action="{{ route('admin.formacoes.destroy', $item) }}"
                                  data-confirma="Excluir a formação &quot;{{ $item->titulo }}&quot;? Isso não pode ser desfeito."
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