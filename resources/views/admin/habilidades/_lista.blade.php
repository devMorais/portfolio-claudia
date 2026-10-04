@if ($itens->isEmpty())
    <p class="vazio">{{ $mensagemVazia }}</p>
@else
    <ol class="habilidades-admin">
        @foreach ($itens as $item)
            <li class="habilidade-admin" id="h-{{ $item->id }}">
                <div class="habilidade-admin__info">
                    <span class="habilidade-admin__nome">{{ $item->nome }}</span>
                    <span class="habilidade-admin__meta">
                        @if ($item->tipo === 'tecnica')
                            {{ $item->categoria ?: 'sem categoria' }} &middot; {{ $item->nivel ?: 'sem nível' }}
                        @else
                            {{ \Illuminate\Support\Str::limit((string) $item->descricao, 90) ?: 'sem descrição' }}
                        @endif
                    </span>
                </div>

                <div class="habilidade-admin__acoes">
                    <form method="POST" action="{{ route('admin.habilidades.subir', $item) }}">
                        @csrf
                        <button type="submit" class="botao-mini" title="Subir"
                                aria-label="Subir {{ $item->nome }}" @disabled($loop->first)>&uarr;</button>
                    </form>

                    <form method="POST" action="{{ route('admin.habilidades.descer', $item) }}">
                        @csrf
                        <button type="submit" class="botao-mini" title="Descer"
                                aria-label="Descer {{ $item->nome }}" @disabled($loop->last)>&darr;</button>
                    </form>

                    <a href="{{ route('admin.habilidades.edit', $item) }}" class="botao-mini">Editar</a>

                    <form method="POST" action="{{ route('admin.habilidades.destroy', $item) }}"
                          data-confirma="Excluir a habilidade &quot;{{ $item->nome }}&quot;? Isso não pode ser desfeito."
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