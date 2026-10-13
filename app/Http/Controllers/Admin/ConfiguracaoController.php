<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ConfiguracaoRequest;
use App\Models\Configuracao;
use App\Models\Secao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ConfiguracaoController extends Controller
{
    public function edit(): View
    {
        return view('admin.configuracoes', [
            'valores' => Configuracao::todas(),
            'secoes' => Secao::orderBy('ordem')->get(),
            'limites' => ConfiguracaoRequest::LIMITES,
        ]);
    }

    public function update(ConfiguracaoRequest $request): RedirectResponse
    {
        $dados = $request->validated();

        DB::transaction(function () use ($dados) {
            foreach (ConfiguracaoRequest::CAMPOS as $chave => [$grupo, $rotulo]) {
                Configuracao::updateOrCreate(
                    ['chave' => $chave],
                    ['grupo' => $grupo, 'rotulo' => $rotulo, 'valor' => (string) ($dados[$chave] ?? '')],
                );
            }

            // Checkbox desmarcado nao e enviado: so as ligadas chegam aqui.
            $ligadas = $dados['secoes_visiveis'] ?? [];

            foreach (Secao::all() as $secao) {
                $secao->update([
                    // A Apresentacao (hero) nunca desliga.
                    'visivel' => $secao->slug === 'hero' || array_key_exists($secao->slug, $ligadas),
                    'titulo_menu' => $dados['titulo_menu'][$secao->slug] ?? $secao->titulo_menu,
                ]);
            }
        });

        return redirect()
            ->route('admin.configuracoes.edit')
            ->with('sucesso', 'Configurações salvas.');
    }
}