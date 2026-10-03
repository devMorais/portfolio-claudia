<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AtualizarTextosRequest;
use App\Models\SecaoHero;
use App\Models\SecaoSobre;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TextosController extends Controller
{
    public function edit(): View
    {
        return view('admin.textos', [
            'hero' => SecaoHero::firstOrNew(['id' => 1]),
            'sobre' => SecaoSobre::firstOrNew(['id' => 1]),
            'limites' => AtualizarTextosRequest::LIMITES,
        ]);
    }

    public function update(AtualizarTextosRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        SecaoHero::updateOrCreate(['id' => 1], $dados['hero']);
        SecaoSobre::updateOrCreate(['id' => 1], $dados['sobre']);

        return redirect()
            ->route('admin.textos.edit')
            ->with('sucesso', 'Textos salvos. O site público já mostra o texto novo.');
    }
}