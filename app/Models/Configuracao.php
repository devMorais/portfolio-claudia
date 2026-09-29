<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'configuracoes';

    protected $guarded = ['id'];

    /** Le uma configuracao pela chave, com valor padrao. */
    public static function valor(string $chave, ?string $padrao = null): ?string
    {
        return static::query()->where('chave', $chave)->value('valor') ?? $padrao;
    }

    /** Todas as configuracoes como array chave => valor (uma consulta so). */
    public static function todas(): array
    {
        return static::query()->pluck('valor', 'chave')->all();
    }
}
