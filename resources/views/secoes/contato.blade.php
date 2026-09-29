{{--
    Contato.

    Sem formulario, por decisao do projeto: recrutador prefere responder no
    canal que ele ja usa (e-mail, LinkedIn, WhatsApp) a preencher campo em
    site. Menos codigo para manter e nenhuma mensagem presa num banco que
    ninguem abre.
--}}
<section class="secao {{ $banda ? 'secao--banda' : '' }}" id="contato">
    <div class="container">
        <div class="contato__chamada" data-revelar>
            <span class="secao__rotulo">Contato</span>
            <h2 class="secao__titulo">Vamos conversar</h2>
            <p class="secao__subtitulo">
                Estou procurando oportunidade em desenvolvimento web.
                Escolha o canal que voce preferir.
            </p>

            <div class="contato__canais">
                @if (! empty($config['email']))
                    <a href="mailto:{{ $config['email'] }}" class="contato__canal">
                        <x-icone-email />
                        <span>
                            <span class="contato__canal-rotulo">E-mail</span>
                            <span class="contato__canal-valor">{{ $config['email'] }}</span>
                        </span>
                    </a>
                @endif

                @if ($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" class="contato__canal" target="_blank" rel="noopener">
                        <x-icone-whatsapp />
                        <span>
                            <span class="contato__canal-rotulo">WhatsApp</span>
                            <span class="contato__canal-valor">Mandar mensagem</span>
                        </span>
                    </a>
                @endif

                @if (! empty($config['linkedin_url']))
                    <a href="{{ $config['linkedin_url'] }}" class="contato__canal" target="_blank" rel="noopener">
                        <x-icone-linkedin />
                        <span>
                            <span class="contato__canal-rotulo">LinkedIn</span>
                            <span class="contato__canal-valor">Ver perfil</span>
                        </span>
                    </a>
                @endif

                @if (! empty($config['github_url']))
                    <a href="{{ $config['github_url'] }}" class="contato__canal" target="_blank" rel="noopener">
                        <x-icone-github />
                        <span>
                            <span class="contato__canal-rotulo">GitHub</span>
                            <span class="contato__canal-valor">Ver o codigo</span>
                        </span>
                    </a>
                @endif
            </div>

            @if (! empty($config['curriculo_url']))
                <div class="contato__acoes">
                    <a href="{{ $config['curriculo_url'] }}" class="btn btn--principal"
                       target="_blank" rel="noopener">
                        Baixar o meu curriculo
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
