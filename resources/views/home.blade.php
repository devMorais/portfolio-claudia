@extends('layouts.publico')

{{--
    A home e uma pagina unica: cada secao e uma ancora.

    O @if em volta de cada include le a tabela secoes, entao ligar/desligar
    uma secao no painel realmente tira ela da pagina — e o menu do header,
    que se monta da mesma lista, acompanha sozinho.

    As bandas de fundo alternam pelo indice das secoes VISIVEIS, nao pela
    posicao fixa. Assim, desligar uma secao do meio nao deixa dois fundos
    iguais colados. (Mesma solucao usada no projeto Dolen.)
--}}

@section('conteudo')
    @php
        $indice = 0;
    @endphp

    @foreach ($secoes as $slug => $secao)
        @php
            // O hero tem fundo proprio e nao entra no rodizio de bandas:
            // por isso ele nao incrementa o contador. Assim a primeira secao
            // depois dele sempre comeca a alternancia, e desligar uma secao
            // do meio no painel nunca deixa dois fundos iguais colados.
            $banda = false;

            if ($slug !== 'hero') {
                $banda = $indice % 2 === 1;
                $indice++;
            }
        @endphp

        @includeIf("secoes.{$slug}", ['banda' => $banda])
    @endforeach
@endsection
