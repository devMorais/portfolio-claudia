<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Habilidade;
use App\Models\Projeto;
use App\Models\Secao;
use Illuminate\Contracts\View\View;

/**
 * Tela inicial do painel.
 *
 * Os numeros sao contados no banco (COUNT), nao carregando as listas inteiras
 * para contar no PHP. Com pouco dado a diferenca nao aparece; o habito e que
 * importa, porque com muito dado aparece na hora.
 */
class PainelController extends Controller
{
    public function index(): View
    {
        return view('admin.painel', [
            'totalProjetos' => Projeto::count(),
            'totalHabilidades' => Habilidade::count(),
            'totalSecoesVisiveis' => Secao::where('visivel', true)->count(),
        ]);
    }
}
