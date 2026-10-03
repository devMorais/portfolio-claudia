{{--
    Minha historia: a migracao da radiologia para a tecnologia.

    Esta e a secao que diferencia este portfolio de qualquer outro portfolio
    de desenvolvedora iniciante. A narrativa em 3 atos evita o paragrafao
    corrido, e o bloco escuro no fim transforma a primeira carreira em
    argumento tecnico, nao em lacuna no curriculo.
--}}
<section class="secao {{ $banda ? 'secao--banda' : '' }}" id="sobre">
    <div class="container">
        <div class="secao__cabecalho" data-revelar>
            <span class="secao__rotulo">Minha história</span>
            <h2 class="secao__titulo">{{ $sobre?->titulo }}</h2>

            @if ($sobre?->subtitulo)
                <p class="secao__subtitulo">{{ $sobre->subtitulo }}</p>
            @endif
        </div>

        <div class="historia__atos">
            @foreach ([1, 2, 3] as $n)
                @php
                    $titulo = $sobre?->{"ato_{$n}_titulo"};
                    $texto = $sobre?->{"ato_{$n}_texto"};
                @endphp

                @if ($titulo || $texto)
                    <article class="ato" data-revelar>
                        <span class="ato__numero">{{ $n }}</span>
                        <h3 class="ato__titulo">{{ $titulo }}</h3>
                        <p class="ato__texto">{{ $texto }}</p>
                    </article>
                @endif
            @endforeach
        </div>

        @if ($habilidadesHumanas->isNotEmpty())
            <div class="trazido" data-revelar>
                <h3 class="trazido__titulo">
                    O que a radiologia me ensinou e eu uso todo dia escrevendo código
                </h3>

                <div class="trazido__grade">
                    @foreach ($habilidadesHumanas as $habilidade)
                        <div class="trazido__item">
                            <h4 class="trazido__nome">{{ $habilidade->nome }}</h4>
                            <p class="trazido__texto">{{ $habilidade->descricao }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
