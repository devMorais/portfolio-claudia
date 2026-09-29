<footer class="footer">
    <div class="container">
        <div class="footer__grade">
            <div>
                <div class="footer__marca">{{ $config['nome_completo'] ?? 'Claudia' }}</div>
                <p class="footer__texto">
                    {{ $config['titulo_profissional'] ?? '' }}@if (! empty($config['cidade'])) &middot; {{ $config['cidade'] }}@endif
                </p>

                <div class="footer__redes">
                    @if (! empty($config['linkedin_url']))
                        <a href="{{ $config['linkedin_url'] }}" class="footer__rede"
                           target="_blank" rel="noopener" aria-label="LinkedIn">
                            <x-icone-linkedin />
                        </a>
                    @endif

                    @if (! empty($config['github_url']))
                        <a href="{{ $config['github_url'] }}" class="footer__rede"
                           target="_blank" rel="noopener" aria-label="GitHub">
                            <x-icone-github />
                        </a>
                    @endif

                    @if (! empty($config['instagram_url']))
                        <a href="{{ $config['instagram_url'] }}" class="footer__rede"
                           target="_blank" rel="noopener" aria-label="Instagram">
                            <x-icone-instagram />
                        </a>
                    @endif

                    @if ($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" class="footer__rede"
                           target="_blank" rel="noopener" aria-label="WhatsApp">
                            <x-icone-whatsapp />
                        </a>
                    @endif
                </div>
            </div>

            <div>
                <h2 class="footer__titulo">Navegar</h2>
                <ul class="footer__lista">
                    @foreach ($secoes as $secao)
                        @if ($secao->titulo_menu)
                            <li><a href="#{{ $secao->slug }}">{{ $secao->titulo_menu }}</a></li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="footer__rodape">
            {{-- Ano calculado, nao escrito na mao: senao em janeiro o site
                 fica com a data velha e passa impressao de abandonado. --}}
            <span>&copy; {{ now()->year }} {{ $config['nome_completo'] ?? '' }}</span>
            <span>Feito com Laravel</span>
        </div>
    </div>
</footer>
