@php
    [$grupo, $coluna] = explode('.', $nome);
    $id = $grupo . '_' . $coluna;
    $limite = $limites[$nome];
    $valor = old($nome, $modelo->{$coluna});
    $temErro = $errors->has($nome);
@endphp

<div class="campo {{ $temErro ? 'campo--erro' : '' }}">
    <label class="campo__rotulo" for="{{ $id }}">
        {{ $rotulo }}@if (! empty($obrigatorio)) <span aria-hidden="true">*</span>@endif
    </label>

    @if (! empty($dica))
        <p class="campo__dica">{{ $dica }}</p>
    @endif

    @if (($tipo ?? 'linha') === 'area')
        <textarea id="{{ $id }}" name="{{ $grupo }}[{{ $coluna }}]"
                  class="campo__entrada" rows="{{ $linhas ?? 5 }}"
                  maxlength="{{ $limite }}" data-contador
                  @if ($temErro) aria-invalid="true" @endif
                  aria-describedby="{{ $id }}-erro">{{ $valor }}</textarea>
    @else
        <input type="text" id="{{ $id }}" name="{{ $grupo }}[{{ $coluna }}]"
               class="campo__entrada" value="{{ $valor }}"
               maxlength="{{ $limite }}" data-contador
               @if ($temErro) aria-invalid="true" @endif
               aria-describedby="{{ $id }}-erro">
    @endif

    <div class="campo__rodape">
        <span class="campo__erro" id="{{ $id }}-erro">@error($nome){{ $message }}@enderror</span>
        <span class="campo__contador" data-contador-alvo="{{ $id }}">{{ mb_strlen((string) $valor) }}/{{ $limite }}</span>
    </div>
</div>