@extends('layouts.admin')

@section('titulo', $formacao->exists ? 'Editar formação' : 'Nova formação')

@section('conteudo')
    <h1 class="painel__titulo">{{ $formacao->exists ? 'Editar formação' : 'Nova formação' }}</h1>
    <p class="painel__subtitulo">
        <a href="{{ route('admin.formacoes.index') }}">&larr; Voltar para a lista</a>
    </p>

    @if ($errors->any())
        <div class="aviso aviso--erro" role="alert">
            Não salvei. Corrija os campos marcados abaixo. O que você digitou continua na tela.
        </div>
    @endif

    <form method="POST"
          action="{{ $formacao->exists ? route('admin.formacoes.update', $formacao) : route('admin.formacoes.store') }}">
        @csrf
        @if ($formacao->exists)
            @method('PUT')
        @endif

        <section class="bloco">
            @include('admin._campo-simples', [
                'nome' => 'titulo', 'rotulo' => 'Título', 'obrigatorio' => true,
                'valor' => $formacao->titulo, 'limite' => $limites['titulo'],
                'dica' => 'Ex.: Inteligência Artificial.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'instituicao', 'rotulo' => 'Instituição',
                'valor' => $formacao->instituicao, 'limite' => $limites['instituicao'],
                'dica' => 'Ex.: SENAC-DF, Curso livre.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'ano', 'rotulo' => 'Ano',
                'valor' => $formacao->ano, 'limite' => $limites['ano'],
                'dica' => 'Ex.: 2026, 2022 - 2024, 2026 (em andamento).',
            ])

            @include('admin._campo-simples', [
                'nome' => 'carga_horaria', 'rotulo' => 'Carga horária',
                'valor' => $formacao->carga_horaria, 'limite' => $limites['carga_horaria'],
                'dica' => 'Ex.: 40h.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'certificado_url', 'rotulo' => 'Link do certificado',
                'valor' => $formacao->certificado_url,
                'dica' => 'Opcional. Começa com https://. Se preencher, o título vira link no site.',
            ])
        </section>

        <div class="painel__acoes">
            <button type="submit" class="painel__botao">
                {{ $formacao->exists ? 'Salvar alterações' : 'Criar formação' }}
            </button>
            <a href="{{ route('admin.formacoes.index') }}" class="painel__link">Cancelar</a>
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