<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO: tudo vem da tabela configuracoes, editavel pelo painel --}}
    <title>{{ $config['seo_titulo'] ?? config('app.name') }}</title>
    <meta name="description" content="{{ $config['seo_descricao'] ?? '' }}">

    {{-- Open Graph: e isto que monta o cartao ao colar o link no LinkedIn
         e no WhatsApp. A imagem precisa ser 1200x630, ou corta errado. --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ $config['seo_titulo'] ?? config('app.name') }}">
    <meta property="og:description" content="{{ $config['seo_descricao'] ?? '' }}">
    @if (! empty($config['seo_og_imagem']))
        <meta property="og:image" content="{{ $config['seo_og_imagem'] }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    {{-- JSON-LD: e o que faz o Google entender que o site e sobre uma
         profissional, e nao uma loja ou um blog qualquer.

         Montado no HomeController, nao aqui: as chaves do schema.org comecam
         com arroba ('@context', '@type') e o Blade tentaria compilar isso
         como diretiva, quebrando a pagina com ParseError. --}}
    <script type="application/ld+json">{!! $jsonLd !!}</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>

@include('parciais.header')

<main>
    @yield('conteudo')
</main>

@include('parciais.footer')

{{-- Botao fixo de WhatsApp, so no mobile e so se o numero foi preenchido --}}
@if ($whatsappUrl)
    <a href="{{ $whatsappUrl }}" class="whatsapp-fixo" target="_blank" rel="noopener"
       aria-label="Falar no WhatsApp">
        <x-icone-whatsapp />
    </a>
@endif

</body>
</html>
