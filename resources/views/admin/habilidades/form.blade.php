@extends('layouts.admin')

@section('titulo', $habilidade->exists ? 'Editar habilidade' : 'Nova habilidade')

@section('conteudo')
    <h1 class="painel__titulo">{{ $habilidade->exists ? 'Editar habilidade' : 'Nova habilidade' }}</h1>
    <p class="painel__subtitulo">
        <a href="{{ route('admin.habilidades.index') }}">&larr; Voltar para a lista</a>
    </p>

    @if ($errors->any())
        <div class="aviso aviso--erro" role="alert">
            Não salvei. Corrija os campos marcados abaixo. O que você digitou continua na tela.
        </div>
    @endif

    <form method="POST"
          action="{{ $habilidade->exists ? route('admin.habilidades.update', $habilidade) : route('admin.habilidades.store') }}">
        @csrf
        @if ($habilidade->exists)
            @method('PUT')
        @endif

        <section class="bloco">
            @include('admin._campo-simples', [
                'nome' => 'nome', 'rotulo' => 'Nome', 'obrigatorio' => true,
                'valor' => $habilidade->nome, 'limite' => $limites['nome'],
                'dica' => 'Ex.: Laravel, Git, Atenção a detalhe.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'tipo', 'rotulo' => 'Tipo', 'obrigatorio' => true,
                'modo' => 'select', 'opcoes' => $tipos, 'valor' => $habilidade->tipo,
                'dica' => 'Técnica aparece na seção Habilidades. Humana aparece na seção Minha história.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'categoria', 'rotulo' => 'Categoria',
                'valor' => $habilidade->categoria, 'limite' => $limites['categoria'],
                'sugestoes' => $categorias,
                'dica' => 'Só para técnicas (Frontend, Backend, QA...). Habilidades da mesma categoria ficam juntas no site.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'nivel', 'rotulo' => 'Nível',
                'modo' => 'select', 'opcoes' => $niveis, 'vazio' => '— sem nível —',
                'valor' => $habilidade->nivel,
                'dica' => 'Só para técnicas.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'icone', 'rotulo' => 'Ícone',
                'valor' => $habilidade->icone, 'limite' => $limites['icone'],
                'dica' => 'Só para técnicas. Nome do ícone no devicon, ex.: php, laravel, git. Se não existir, o site mostra sem ícone.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'descricao', 'rotulo' => 'Descrição',
                'modo' => 'area', 'linhas' => 5,
                'valor' => $habilidade->descricao, 'limite' => $limites['descricao'],
                'dica' => 'Usada nas humanas, no card da seção Minha história.',
            ])
        </section>

        <div class="painel__acoes">
            <button type="submit" class="painel__botao">
                {{ $habilidade->exists ? 'Salvar alterações' : 'Criar habilidade' }}
            </button>
            <a href="{{ route('admin.habilidades.index') }}" class="painel__link">Cancelar</a>
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