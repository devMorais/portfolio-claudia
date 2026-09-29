{{--
    Header fixo.

    O menu e montado a partir das secoes VISIVEIS ($secoes, que vem do
    HomeController). Isso e proposital: desligar uma secao no painel tira o
    link do menu junto, e nunca sobra ancora apontando para o vazio.
    Nao troque isso por uma lista escrita na mao aqui.
--}}
<header class="header" data-header>
    <div class="container header__interno">
        <a href="#hero" class="header__marca">
            {{ $config['nome_completo'] ?? 'Claudia' }}<span>.</span>
        </a>

        <button class="header__sanduiche" data-menu-botao
                aria-expanded="false" aria-controls="menu-principal"
                aria-label="Abrir menu">
            <span></span><span></span><span></span>
        </button>

        <nav class="header__nav" id="menu-principal" data-menu>
            @foreach ($secoes as $secao)
                @if ($secao->titulo_menu)
                    <a href="#{{ $secao->slug }}" class="header__link">
                        {{ $secao->titulo_menu }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>
</header>
