<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trajetoria extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'trajetorias';

    protected $guarded = ['id'];
}
