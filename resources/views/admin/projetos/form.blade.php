@extends('layouts.admin')

@section('titulo', $projeto->exists ? 'Editar projeto' : 'Novo projeto')

@section('conteudo')
    <h1 class="painel__titulo">{{ $projeto->exists ? 'Editar projeto' : 'Novo projeto' }}</h1>
    <p class="painel__subtitulo">
        <a href="{{ route('admin.projetos.index') }}">&larr; Voltar para a lista</a>
    </p>

    @if ($errors->any())
        <div class="aviso aviso--erro" role="alert">
            Não salvei. Corrija os campos marcados abaixo. O que você digitou continua na tela
            (se escolheu uma imagem, escolha o arquivo de novo).
        </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $projeto->exists ? route('admin.projetos.update', $projeto) : route('admin.projetos.store') }}">
        @csrf
        @if ($projeto->exists)
            @method('PUT')
        @endif

        <section class="bloco">
            @include('admin._campo-simples', [
                'nome' => 'nome', 'rotulo' => 'Nome do projeto', 'obrigatorio' => true,
                'valor' => $projeto->nome, 'limite' => $limites['nome'],
            ])

            @include('admin._campo-simples', [
                'nome' => 'resumo', 'rotulo' => 'Resumo',
                'modo' => 'area', 'linhas' => 3,
                'valor' => $projeto->resumo, 'limite' => $limites['resumo'],
                'dica' => 'Uma ou duas frases sobre o que o projeto faz.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'papel', 'rotulo' => 'Papel',
                'valor' => $projeto->papel, 'limite' => $limites['papel'],
                'dica' => 'Ex.: projeto próprio, contribuição.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'tecnologias', 'rotulo' => 'Tecnologias',
                'valor' => $projeto->tecnologias, 'limite' => $limites['tecnologias'],
                'dica' => 'Separe por vírgula. Ex.: Angular, Laravel, MySQL.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'repositorio_url', 'rotulo' => 'Link do repositório',
                'valor' => $projeto->repositorio_url,
                'dica' => 'Começa com https://. O recrutador clica nele: deixe só repositório público e com README.',
            ])

            @include('admin._campo-simples', [
                'nome' => 'site_url', 'rotulo' => 'Link do site no ar',
                'valor' => $projeto->site_url,
                'dica' => 'Opcional. Se preencher, aparece o link "Ver no ar".',
            ])

            <div class="campo {{ $errors->has('imagem') ? 'campo--erro' : '' }}">
                <label class="campo__rotulo" for="campo_imagem">Captura de tela</label>
                <p class="campo__dica">JPG, PNG ou WEBP, até 2 MB. Proporção 16:10 (ex.: 1280x800) fica melhor no site.</p>

                @if ($projeto->imagem_url)
                    <img src="{{ $projeto->imagem_url }}" alt="Imagem atual do projeto"
                         style="display: block; width: 100%; max-width: 240px; margin-bottom: 12px; border-radius: 8px;">

                    <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px; font-size: 0.875rem;">
                        <input type="checkbox" name="remover_imagem" value="1" @checked(old('remover_imagem'))>
                        Remover a imagem atual
                    </label>
                @endif

                <input type="file" id="campo_imagem" name="imagem" class="campo__entrada"
                       accept="image/jpeg,image/png,image/webp"
                       @if ($errors->has('imagem')) aria-invalid="true" @endif
                       aria-describedby="campo_imagem-erro">

                <div class="campo__rodape">
                    <span class="campo__erro" id="campo_imagem-erro">@error('imagem'){{ $message }}@enderror</span>
                </div>
            </div>
        </section>

        <div class="painel__acoes">
            <button type="submit" class="painel__botao">
                {{ $projeto->exists ? 'Salvar alterações' : 'Criar projeto' }}
            </button>
            <a href="{{ route('admin.projetos.index') }}" class="painel__link">Cancelar</a>
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