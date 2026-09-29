<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecaoHero extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'secao_hero';

    protected $guarded = ['id'];
}
