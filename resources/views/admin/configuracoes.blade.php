@extends('layouts.admin')

@section('titulo', 'Configurações')

@section('conteudo')
    <h1 class="painel__titulo">Configurações</h1>
    <p class="painel__subtitulo">
        Contato, redes, SEO e as seções do site.
        <a href="{{ route('home') }}" target="_blank" rel="noopener">Ver o site</a>
    </p>

    @if (session('sucesso'))
        <div class="aviso aviso--sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    @if ($errors->any())
        <div class="aviso aviso--erro" role="alert">
            Não salvei. Corrija os campos marcados abaixo. O que você digitou continua na tela.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.configuracoes.update') }}">
        @csrf
        @method('PUT')

        <section class="bloco">
            <h2 class="bloco__titulo">Dados gerais</h2>

            @include('admin._campo-simples', [
                'nome' => 'nome_completo', 'rotulo' => 'Nome completo', 'obrigatorio' => true,
                'valor' => $valores['nome_completo'] ?? '', 'limite' => $limites['nome_completo'],
            ])

            @include('admin._campo-simples', [
                'nome' => 'titulo_profissional', 'rotulo' => 'Título profissional', 'obrigatorio' => true,
                'valor' => $valores['titulo_profissional'] ?? '', 'limite' => $limites['titulo_profissional'],
                'dica' => 'Ex.: Desenvolvedora front-end.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'cidade', 'rotulo' => 'Cidade',
                'valor' => $valores['cidade'] ?? '', 'limite' => $limites['cidade'],
            ])

            @include('admin._campo-simples', [
                'nome' => 'email', 'rotulo' => 'E-mail de contato', 'obrigatorio' => true,
                'valor' => $valores['email'] ?? '', 'limite' => $limites['email'],
            ])

            @include('admin._campo-simples', [
                'nome' => 'curriculo_url', 'rotulo' => 'Link do currículo (PDF)',
                'valor' => $valores['curriculo_url'] ?? '',
                'dica' => 'Ex.: /curriculo.pdf (arquivo na pasta public).',
            ])
        </section>

        <section class="bloco">
            <h2 class="bloco__titulo">Redes</h2>

            @include('admin._campo-simples', [
                'nome' => 'whatsapp', 'rotulo' => 'WhatsApp',
                'valor' => $valores['whatsapp'] ?? '',
                'dica' => 'Com DDD. Ex.: 61999998888.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'linkedin_url', 'rotulo' => 'LinkedIn',
                'valor' => $valores['linkedin_url'] ?? '',
                'dica' => 'Começa com https://.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'github_url', 'rotulo' => 'GitHub',
                'valor' => $valores['github_url'] ?? '',
                'dica' => 'Começa com https://.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'instagram_url', 'rotulo' => 'Instagram',
                'valor' => $valores['instagram_url'] ?? '',
                'dica' => 'Começa com https://.',
            ])
        </section>

        <section class="bloco">
            <h2 class="bloco__titulo">SEO (aparece no Google e ao compartilhar)</h2>

            @include('admin._campo-simples', [
                'nome' => 'seo_titulo', 'rotulo' => 'Título da página',
                'valor' => $valores['seo_titulo'] ?? '', 'limite' => $limites['seo_titulo'],
            ])

            @include('admin._campo-simples', [
                'nome' => 'seo_descricao', 'rotulo' => 'Descrição',
                'modo' => 'area', 'linhas' => 3,
                'valor' => $valores['seo_descricao'] ?? '', 'limite' => $limites['seo_descricao'],
                'dica' => 'Uma ou duas frases. O Google mostra até uns 160 caracteres.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'seo_og_imagem', 'rotulo' => 'Imagem de compartilhamento',
                'valor' => $valores['seo_og_imagem'] ?? '',
                'dica' => 'Opcional. Link da imagem, tamanho 1200x630.',
            ])
        </section>

        <section class="bloco">
            <h2 class="bloco__titulo">Seções do site</h2>
            <p class="campo__dica">
                Desmarque para esconder a seção do site e do menu. A Apresentação fica sempre ligada.
            </p>

            @foreach ($secoes as $secao)
                @php
                    $slug = $secao->slug;
                    $fixa = $slug === 'hero';
                    $ligada = $errors->any()
                        ? old("secoes_visiveis.$slug") !== null
                        : (bool) $secao->visivel;
                @endphp

                <div class="campo {{ $errors->has("titulo_menu.$slug") ? 'campo--erro' : '' }}">
                    <label class="campo__rotulo" for="menu_{{ $slug }}">{{ $secao->nome }}</label>

                    <input type="text" id="menu_{{ $slug }}" name="titulo_menu[{{ $slug }}]"
                           class="campo__entrada" maxlength="{{ $limites['titulo_menu'] }}"
                           value="{{ old("titulo_menu.$slug", $secao->titulo_menu) }}"
                           aria-describedby="menu_{{ $slug }}-erro">

                    <label style="display: flex; align-items: center; gap: 8px; margin-top: 8px; font-size: 0.875rem;">
                        <input type="checkbox" name="secoes_visiveis[{{ $slug }}]" value="1"
                               @checked($ligada || $fixa) @disabled($fixa)>
                        Mostrar no site
                    </label>

                    <div class="campo__rodape">
                        <span class="campo__erro" id="menu_{{ $slug }}-erro">@error("titulo_menu.$slug"){{ $message }}@enderror</span>
                    </div>
                </div>
            @endforeach
        </section>

        <div class="painel__acoes">
            <button type="submit" class="painel__botao">Salvar configurações</button>
        </div>
    </form>

    <script>
        document.querySelectorAll('[data-contador]').forEach(function (campo) {
            var alvo = document.querySelector('[data-contador-alvo="' + campo.id + '"]');
            if (!alvo) return;
            var limite = campo.getAttribute('maxlength');
            campo.addEventListener('input', function () {
                alvo.textContent = campo.value.length + '/' + limite;
            });
        });
    </script>
@endsection