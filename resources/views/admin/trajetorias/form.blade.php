@extends('layouts.admin')

@section('titulo', $trajetoria->exists ? 'Editar item' : 'Novo item')

@section('conteudo')
    <h1 class="painel__titulo">{{ $trajetoria->exists ? 'Editar item' : 'Novo item' }}</h1>
    <p class="painel__subtitulo">
        <a href="{{ route('admin.trajetorias.index') }}">&larr; Voltar para a lista</a>
    </p>

    @if ($errors->any())
        <div class="aviso aviso--erro" role="alert">
            Não salvei. Corrija os campos marcados abaixo. O que você digitou continua na tela.
        </div>
    @endif

    <form method="POST"
          action="{{ $trajetoria->exists ? route('admin.trajetorias.update', $trajetoria) : route('admin.trajetorias.store') }}">
        @csrf
        @if ($trajetoria->exists)
            @method('PUT')
        @endif

        <section class="bloco">
            @include('admin._campo-simples', [
                'nome' => 'periodo', 'rotulo' => 'Período', 'obrigatorio' => true,
                'valor' => $trajetoria->periodo, 'limite' => $limites['periodo'],
                'dica' => 'Ex.: 2022 - 2024, 2026 - atual.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'titulo', 'rotulo' => 'Título', 'obrigatorio' => true,
                'valor' => $trajetoria->titulo, 'limite' => $limites['titulo'],
                'dica' => 'Ex.: Tecnologia em Radiologia.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'subtitulo', 'rotulo' => 'Subtítulo',
                'valor' => $trajetoria->subtitulo, 'limite' => $limites['subtitulo'],
                'dica' => 'A empresa ou a instituição.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'area', 'rotulo' => 'Área', 'obrigatorio' => true,
                'modo' => 'select', 'opcoes' => $areas, 'valor' => $trajetoria->area,
                'dica' => 'Muda a cor do item na linha do tempo do site.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'descricao', 'rotulo' => 'Descrição',
                'modo' => 'area', 'linhas' => 5,
                'valor' => $trajetoria->descricao, 'limite' => $limites['descricao'],
                'dica' => 'Duas ou três frases sobre o que você fez.',
            ])
        </section>

        <div class="painel__acoes">
            <button type="submit" class="painel__botao">
                {{ $trajetoria->exists ? 'Salvar alterações' : 'Criar item' }}
            </button>
            <a href="{{ route('admin.trajetorias.index') }}" class="painel__link">Cancelar</a>
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