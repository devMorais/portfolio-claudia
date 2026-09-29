<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Habilidade extends Model
{
    /** Tabela em portugues — o Eloquent nao acerta o plural de nome PT sozinho. */
    protected $table = 'habilidades';

    protected $guarded = ['id'];

    /** Habilidades tecnicas (Laravel, PHP, Git...). */
    public function scopeTecnicas($query)
    {
        return $query->where('tipo', 'tecnica')->orderBy('ordem');
    }

    /** Habilidades humanas trazidas da radiologia — o diferencial dela. */
    public function scopeHumanas($query)
    {
        return $query->where('tipo', 'humana')->orderBy('ordem');
    }
}
