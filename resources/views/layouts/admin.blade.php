<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- noindex: painel nunca pode aparecer em busca --}}
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('titulo', 'Painel') &middot; {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss'])
</head>
<body>

<div class="painel">
    <aside class="painel__lateral">
        <div class="painel__marca">Painel</div>

        <nav class="painel__menu">
            <a href="{{ route('admin.painel') }}"
               class="painel__item {{ request()->routeIs('admin.painel') ? 'painel__item--ativo' : '' }}">
                Inicio
            </a>

            <a href="{{ route('admin.textos.edit') }}"
               class="painel__item {{ request()->routeIs('admin.textos.*') ? 'painel__item--ativo' : '' }}">
                Textos
            </a>

            <a href="{{ route('admin.habilidades.index') }}"
               class="painel__item {{ request()->routeIs('admin.habilidades.*') ? 'painel__item--ativo' : '' }}">
                Habilidades
            </a>

            {{--
                As telas abaixo ainda nao existem: sao as cards PC-15 a PC-17
                no board do Avante. Ficam visiveis e apagadas de proposito,
                para o menu ja mostrar o desenho final do painel e a Claudia
                saber exatamente o que falta construir.
                Quando uma tela ficar pronta, troque o <span> por um <a>,
                no mesmo modelo dos itens acima.
            --}}
            <span class="painel__item painel__item--pendente" title="Card PC-15">Trajetoria</span>
            <span class="painel__item painel__item--pendente" title="Card PC-16">Projetos</span>
            <span class="painel__item painel__item--pendente" title="Card PC-17">Configuracoes</span>
        </nav>

        <div class="painel__usuario">
            {{ auth()->user()?->name }}

            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn" style="padding-inline: 0; min-height: 36px; color: inherit; font-size: inherit;">
                    Sair
                </button>
            </form>
        </div>
    </aside>

    <main class="painel__conteudo">
        @yield('conteudo')
    </main>
</div>

</body>
</html>