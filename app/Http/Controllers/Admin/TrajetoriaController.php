<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TrajetoriaRequest;
use App\Models\Trajetoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TrajetoriaController extends Controller
{
    public function index(): View
    {
        return view('admin.trajetorias.index', [
            'itens' => Trajetoria::orderBy('ordem')->orderBy('id')->get(),
            'areas' => TrajetoriaRequest::AREAS,
        ]);
    }

    public function create(): View
    {
        return $this->formulario(new Trajetoria(['area' => 'tecnologia']));
    }

    public function store(TrajetoriaRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        // Item novo entra no fim da linha do tempo. A Claudia ajusta com as setas.
        $dados['ordem'] = (int) Trajetoria::max('ordem') + 1;

        $trajetoria = Trajetoria::create($dados);

        return $this->voltar($trajetoria, "Item \"{$trajetoria->titulo}\" criado.");
    }

    public function edit(Trajetoria $trajetoria): View
    {
        return $this->formulario($trajetoria);
    }

    public function update(TrajetoriaRequest $request, Trajetoria $trajetoria): RedirectResponse
    {
        $trajetoria->update($request->validated());

        return $this->voltar($trajetoria, "Item \"{$trajetoria->titulo}\" salvo.");
    }

    public function destroy(Trajetoria $trajetoria): RedirectResponse
    {
        $titulo = $trajetoria->titulo;
        $trajetoria->delete();

        return redirect()
            ->route('admin.trajetorias.index')
            ->with('sucesso', "Item \"{$titulo}\" excluído.");
    }

    public function subir(Trajetoria $trajetoria): RedirectResponse
    {
        return $this->mover($trajetoria, -1);
    }

    public function descer(Trajetoria $trajetoria): RedirectResponse
    {
        return $this->mover($trajetoria, 1);
    }

    /**
     * Troca o item de lugar com o vizinho e renumera a lista inteira
     * (1, 2, 3...). Renumerar conserta ordem repetida ou com buraco,
     * entao a ordem nunca fica ambigua no site.
     */
    private function mover(Trajetoria $trajetoria, int $passo): RedirectResponse
    {
        DB::transaction(function () use ($trajetoria, $passo) {
            $ids = Trajetoria::orderBy('ordem')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $posicao = array_search($trajetoria->id, $ids);
            $destino = $posicao + $passo;

            if ($posicao !== false && isset($ids[$destino])) {
                [$ids[$posicao], $ids[$destino]] = [$ids[$destino], $ids[$posicao]];
            }

            foreach ($ids as $indice => $id) {
                Trajetoria::whereKey($id)->update(['ordem' => $indice + 1]);
            }
        });

        return $this->voltar($trajetoria);
    }

    private function formulario(Trajetoria $trajetoria): View
    {
        return view('admin.trajetorias.form', [
            'trajetoria' => $trajetoria,
            'areas' => TrajetoriaRequest::AREAS,
            'limites' => TrajetoriaRequest::LIMITES,
        ]);
    }

    /** Volta para a lista, ja rolando ate o item mexido (ajuda no celular). */
    private function voltar(Trajetoria $trajetoria, ?string $mensagem = null): RedirectResponse
    {
        $resposta = redirect()->to(route('admin.trajetorias.index') . '#t-' . $trajetoria->id);

        return $mensagem ? $resposta->with('sucesso', $mensagem) : $resposta;
    }
}