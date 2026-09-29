<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formacao extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'formacoes';

    protected $guarded = ['id'];
}
