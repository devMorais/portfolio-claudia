{{--
    Projetos: a secao que sustenta tudo o que o resto do site afirma.

    Regra que vale mais que este codigo: todo projeto listado precisa ter
    repositorio publico com README decente. O recrutador clica. Repositorio
    sem README ou com commit "teste teste" derruba a impressao que o site
    inteiro construiu.
--}}
<section class="secao {{ $banda ? 'secao--banda' : '' }}" id="projetos">
    <div class="container">
        <div class="secao__cabecalho" data-revelar>
            <span class="secao__rotulo">Projetos</span>
            <h2 class="secao__titulo">O que eu construi</h2>
            <p class="secao__subtitulo">
                Codigo aberto para quem quiser olhar de perto.
            </p>
        </div>

        @if ($projetos->isNotEmpty())
            <div class="projetos__grade">
                @foreach ($projetos as $projeto)
                    <article class="projeto" data-revelar>
                        @if ($projeto->imagem_url)
                            <div class="projeto__imagem">
                                {{-- loading lazy: imagem abaixo da primeira tela nao
                                     precisa competir pelo carregamento inicial --}}
                                <img src="{{ $projeto->imagem_url }}"
                                     alt="Tela do projeto {{ $projeto->nome }}"
                                     loading="lazy" width="640" height="400">
                            </div>
                        @else
                            <div class="projeto__imagem projeto__imagem--vazia">
                                Sem captura de tela ainda
                            </div>
                        @endif

                        <div class="projeto__corpo">
                            @if ($projeto->papel)
                                <div class="projeto__papel">
                                    <span class="etiqueta etiqueta--destaque">{{ $projeto->papel }}</span>
                                </div>
                            @endif

                            <h3 class="projeto__nome">{{ $projeto->nome }}</h3>

                            @if ($projeto->resumo)
                                <p class="projeto__resumo">{{ $projeto->resumo }}</p>
                            @endif

                            @if ($projeto->tecnologias)
                                <div class="projeto__techs">
                                    @foreach (explode(',', $projeto->tecnologias) as $tech)
                                        <span class="etiqueta">{{ trim($tech) }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="projeto__acoes">
                                @if ($projeto->repositorio_url)
                                    <a href="{{ $projeto->repositorio_url }}" class="projeto__link"
                                       target="_blank" rel="noopener">
                                        <x-icone-github /> Codigo
                                    </a>
                                @endif

                                @if ($projeto->site_url)
                                    <a href="{{ $projeto->site_url }}" class="projeto__link"
                                       target="_blank" rel="noopener">
                                        Ver no ar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <p class="vazio">Nenhum projeto cadastrado ainda.</p>
        @endif
    </div>
</section>
