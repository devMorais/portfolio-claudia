{{--
    Habilidades tecnicas, agrupadas por categoria.

    O nivel aparece como rotulo (Basico / Intermediario / Avancado), nao como
    porcentagem. "Laravel 85%" nao significa nada e cria uma expectativa que
    a entrevista tecnica vai cobrar. Lista curta e verdadeira passa mais
    confianca do que lista longa e furada.
--}}
<section class="secao {{ $banda ? 'secao--banda' : '' }}" id="habilidades">
    <div class="container">
        <div class="secao__cabecalho" data-revelar>
            <span class="secao__rotulo">Habilidades</span>
            <h2 class="secao__titulo">Com o que eu trabalho</h2>
        </div>

        @forelse ($habilidades as $categoria => $itens)
            <div class="habilidades__grupo" data-revelar>
                @if ($categoria)
                    <h3 class="habilidades__categoria">{{ $categoria }}</h3>
                @endif

                <div class="habilidades__grade">
                    @foreach ($itens as $habilidade)
                        <div class="habilidade">
                            @if ($habilidade->icone)
                                <span class="habilidade__icone">
                                    {{-- Icones do devicon via CDN. Se um nao existir,
                                         o alt vazio deixa a caixa limpa em vez de
                                         mostrar icone de imagem quebrada. --}}
                                    <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/{{ $habilidade->icone }}/{{ $habilidade->icone }}-original.svg"
                                         alt="" loading="lazy" width="22" height="22">
                                </span>
                            @endif

                            <div>
                                <div class="habilidade__nome">{{ $habilidade->nome }}</div>

                                @if ($habilidade->nivel)
                                    <div class="habilidade__nivel">{{ $habilidade->nivel }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="vazio">Nenhuma habilidade cadastrada ainda.</p>
        @endforelse
    </div>
</section>
