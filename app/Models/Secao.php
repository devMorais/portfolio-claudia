<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Secao extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'secoes';

    protected $guarded = ['id'];

    /** So as secoes ligadas no painel, na ordem definida lá. */
    public function scopeVisiveis($query)
    {
        return $query->where('visivel', true)->orderBy('ordem');
    }
}
