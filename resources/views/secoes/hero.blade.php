{{--
    Hero: a secao mais importante do site.

    Quem abre o link le isto e decide se continua rolando. Tres niveis de
    hierarquia (sobretitulo, titulo grande, paragrafo) e SO DOIS botoes —
    com tres ou mais, o visitante nao escolhe nenhum.
--}}
<section class="hero secao" id="hero">
    <div class="container hero__grade">
        <div>
            @if ($hero?->sobretitulo)
                <span class="hero__sobretitulo">{{ $hero->sobretitulo }}</span>
            @endif

            <h1 class="hero__titulo">{{ $hero?->titulo }}</h1>

            @if ($hero?->texto)
                <p class="hero__texto">{{ $hero->texto }}</p>
            @endif

            <div class="hero__acoes">
                @if (! empty($config['curriculo_url']))
                    <a href="{{ $config['curriculo_url'] }}" class="btn btn--principal"
                       target="_blank" rel="noopener">
                        {{ $hero?->cta_primario_label ?? 'Baixar curriculo' }}
                    </a>
                @endif

                @if ($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" class="btn btn--contorno"
                       target="_blank" rel="noopener">
                        {{ $hero?->cta_secundario_label ?? 'Falar no WhatsApp' }}
                    </a>
                @endif
            </div>

            <div class="hero__links">
                @if (! empty($config['linkedin_url']))
                    <a href="{{ $config['linkedin_url'] }}" target="_blank" rel="noopener">
                        <x-icone-linkedin /> LinkedIn
                    </a>
                @endif

                @if (! empty($config['github_url']))
                    <a href="{{ $config['github_url'] }}" target="_blank" rel="noopener">
                        <x-icone-github /> GitHub
                    </a>
                @endif
            </div>
        </div>

        @if ($hero?->foto_url)
            <div class="hero__foto">
                {{-- Sem lazy aqui: a foto do hero e a primeira coisa na tela,
                     adiar o carregamento dela piora a impressao inicial. --}}
                <img src="{{ $hero->foto_url }}"
                     alt="Foto de {{ $config['nome_completo'] ?? 'Claudia' }}"
                     width="680" height="850">
            </div>
        @else
            <div class="hero__foto hero__foto--vazia">
                Envie a sua foto profissional pelo painel.<br>
                Boa luz, fundo limpo, olhando para a camera.
            </div>
        @endif
    </div>

    <span class="hero__rolar" aria-hidden="true">role</span>
</section>
