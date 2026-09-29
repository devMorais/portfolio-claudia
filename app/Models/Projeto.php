<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Projeto extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'projetos';

    protected $guarded = ['id'];
}
