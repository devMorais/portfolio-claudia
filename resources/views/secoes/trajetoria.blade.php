{{--
    Linha do tempo da carreira + formacao.

    A cor da bolinha e do periodo muda conforme a area (saude ou tecnologia),
    entao o recrutador ve de relance ONDE a tecnologia entra na historia —
    que e exatamente o ponto que ele procura na tela.

    A linha fica de um lado so, inclusive no desktop. Timeline alternando
    lados fica bonita na tela grande e quebra feio no celular; nao vale o
    custo de manutencao num portfolio.
--}}
<section class="secao {{ $banda ? 'secao--banda' : '' }}" id="trajetoria">
    <div class="container">
        <div class="secao__cabecalho" data-revelar>
            <span class="secao__rotulo">Trajetoria</span>
            <h2 class="secao__titulo">O caminho que eu percorri</h2>
        </div>

        @if ($trajetorias->isNotEmpty())
            <ol class="trajetoria__lista">
                @foreach ($trajetorias as $item)
                    <li class="trajetoria__item trajetoria__item--{{ $item->area }}" data-revelar>
                        <span class="trajetoria__periodo">{{ $item->periodo }}</span>
                        <h3 class="trajetoria__titulo">{{ $item->titulo }}</h3>

                        @if ($item->subtitulo)
                            <div class="trajetoria__subtitulo">{{ $item->subtitulo }}</div>
                        @endif

                        @if ($item->descricao)
                            <p class="trajetoria__texto">{{ $item->descricao }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($formacoes->isNotEmpty())
            <div class="formacoes" data-revelar>
                <h3 class="habilidades__categoria">Formacao e certificados</h3>

                <div class="formacoes__grade">
                    @foreach ($formacoes as $formacao)
                        <div class="formacao">
                            <h4 class="formacao__titulo">{{ $formacao->titulo }}</h4>

                            <div class="formacao__meta">
                                {{ collect([
                                    $formacao->instituicao,
                                    $formacao->ano,
                                    $formacao->carga_horaria,
                                ])->filter()->join(' · ') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
