@php
    $id = 'campo_' . $nome;
    $atual = old($nome, $valor ?? '');
    $modo = $modo ?? 'linha';
    $limite = $limite ?? null;
    $temErro = $errors->has($nome);
@endphp

<div class="campo {{ $temErro ? 'campo--erro' : '' }}">
    <label class="campo__rotulo" for="{{ $id }}">
        {{ $rotulo }}@if (! empty($obrigatorio)) <span aria-hidden="true">*</span>@endif
    </label>

    @if (! empty($dica))
        <p class="campo__dica">{{ $dica }}</p>
    @endif

    @if ($modo === 'select')
        <select id="{{ $id }}" name="{{ $nome }}" class="campo__entrada"
                @if ($temErro) aria-invalid="true" @endif
                aria-describedby="{{ $id }}-erro">
            @if (isset($vazio))
                <option value="">{{ $vazio }}</option>
            @endif
            @foreach ($opcoes as $chave => $rotuloOpcao)
                <option value="{{ $chave }}" @selected((string) $atual === (string) $chave)>{{ $rotuloOpcao }}</option>
            @endforeach
        </select>
    @elseif ($modo === 'area')
        <textarea id="{{ $id }}" name="{{ $nome }}" class="campo__entrada"
                  rows="{{ $linhas ?? 4 }}"
                  @if ($limite) maxlength="{{ $limite }}" data-contador @endif
                  @if ($temErro) aria-invalid="true" @endif
                  aria-describedby="{{ $id }}-erro">{{ $atual }}</textarea>
    @else
        <input type="text" id="{{ $id }}" name="{{ $nome }}" class="campo__entrada"
               value="{{ $atual }}"
               @if ($limite) maxlength="{{ $limite }}" data-contador @endif
               @if (! empty($sugestoes)) list="{{ $id }}-lista" @endif
               @if ($temErro) aria-invalid="true" @endif
               aria-describedby="{{ $id }}-erro">

        @if (! empty($sugestoes))
            <datalist id="{{ $id }}-lista">
                @foreach ($sugestoes as $sugestao)
                    <option value="{{ $sugestao }}"></option>
                @endforeach
            </datalist>
        @endif
    @endif

    <div class="campo__rodape">
        <span class="campo__erro" id="{{ $id }}-erro">@error($nome){{ $message }}@enderror</span>
        @if ($limite)
            <span class="campo__contador" data-contador-alvo="{{ $id }}">{{ mb_strlen((string) $atual) }}/{{ $limite }}</span>
        @endif
    </div>
</div>