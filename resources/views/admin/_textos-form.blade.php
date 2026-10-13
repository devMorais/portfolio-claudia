<form method="POST" action="{{ route('admin.textos.update') }}">
    @csrf
    @method('PUT')

    @if (session('sucesso'))
        <div class="aviso aviso--sucesso" role="status">{{ session('sucesso') }}</div>
    @endif

    @if ($errors->any())
        <div class="aviso aviso--erro" role="alert">
            Não salvei. Corrija os campos marcados abaixo. O que você digitou continua na tela.
        </div>
    @endif

    <section class="bloco">
        <h2 class="bloco__titulo">Apresentação (topo do site)</h2>

        @include('admin._campo', [
            'nome' => 'hero.sobretitulo', 'rotulo' => 'Frase acima do título',
            'dica' => 'Ex.: desenvolvedora front-end', 'modelo' => $hero,
        ])
        @include('admin._campo', [
            'nome' => 'hero.titulo', 'rotulo' => 'Título', 'obrigatorio' => true,
            'dica' => 'É a frase grande do site. Mantenha curta.', 'modelo' => $hero,
        ])
        @include('admin._campo', [
            'nome' => 'hero.texto', 'rotulo' => 'Texto de apresentação',
            'tipo' => 'area', 'linhas' => 6, 'modelo' => $hero,
        ])
    </section>

    <section class="bloco">
        <h2 class="bloco__titulo">Minha história</h2>

        @include('admin._campo', [
            'nome' => 'sobre.titulo', 'rotulo' => 'Título da seção',
            'obrigatorio' => true, 'modelo' => $sobre,
        ])
        @include('admin._campo', [
            'nome' => 'sobre.subtitulo', 'rotulo' => 'Subtítulo', 'modelo' => $sobre,
        ])

        @foreach ([1, 2, 3] as $n)
            <fieldset class="ato-edicao">
                <legend class="ato-edicao__legenda">Ato {{ $n }}</legend>

                @include('admin._campo', [
                    'nome' => "sobre.ato_{$n}_titulo", 'rotulo' => 'Título do ato',
                    'modelo' => $sobre,
                ])
                @include('admin._campo', [
                    'nome' => "sobre.ato_{$n}_texto", 'rotulo' => 'Texto do ato',
                    'tipo' => 'area', 'linhas' => 6, 'modelo' => $sobre,
                ])
            </fieldset>
        @endforeach
    </section>

    <div class="painel__acoes">
        <button type="submit" class="painel__botao">Salvar textos</button>
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