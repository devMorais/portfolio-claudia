<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormacaoRequest;
use App\Models\Formacao;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FormacaoController extends Controller
{
    public function index(): View
    {
        return view('admin.formacoes.index', [
            'itens' => Formacao::orderBy('ordem')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->formulario(new Formacao());
    }

    public function store(FormacaoRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        // Item novo entra no fim da lista. A Claudia ajusta com as setas.
        $dados['ordem'] = (int) Formacao::max('ordem') + 1;

        $formacao = Formacao::create($dados);

        return $this->voltar($formacao, "Formação \"{$formacao->titulo}\" criada.");
    }

    public function edit(Formacao $formacao): View
    {
        return $this->formulario($formacao);
    }

    public function update(FormacaoRequest $request, Formacao $formacao): RedirectResponse
    {
        $formacao->update($request->validated());

        return $this->voltar($formacao, "Formação \"{$formacao->titulo}\" salva.");
    }

    public function destroy(Formacao $formacao): RedirectResponse
    {
        $titulo = $formacao->titulo;
        $formacao->delete();

        return redirect()
            ->route('admin.formacoes.index')
            ->with('sucesso', "Formação \"{$titulo}\" excluída.");
    }

    public function subir(Formacao $formacao): RedirectResponse
    {
        return $this->mover($formacao, -1);
    }

    public function descer(Formacao $formacao): RedirectResponse
    {
        return $this->mover($formacao, 1);
    }

    /**
     * Troca o item de lugar com o vizinho e renumera a lista inteira
     * (1, 2, 3...). Renumerar conserta ordem repetida ou com buraco.
     */
    private function mover(Formacao $formacao, int $passo): RedirectResponse
    {
        DB::transaction(function () use ($formacao, $passo) {
            $ids = Formacao::orderBy('ordem')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $posicao = array_search($formacao->id, $ids);
            $destino = $posicao + $passo;

            if ($posicao !== false && isset($ids[$destino])) {
                [$ids[$posicao], $ids[$destino]] = [$ids[$destino], $ids[$posicao]];
            }

            foreach ($ids as $indice => $id) {
                Formacao::whereKey($id)->update(['ordem' => $indice + 1]);
            }
        });

        return $this->voltar($formacao);
    }

    private function formulario(Formacao $formacao): View
    {
        return view('admin.formacoes.form', [
            'formacao' => $formacao,
            'limites' => FormacaoRequest::LIMITES,
        ]);
    }

    /** Volta para a lista, ja rolando ate o item mexido (ajuda no celular). */
    private function voltar(Formacao $formacao, ?string $mensagem = null): RedirectResponse
    {
        $resposta = redirect()->to(route('admin.formacoes.index') . '#f-' . $formacao->id);

        return $mensagem ? $resposta->with('sucesso', $mensagem) : $resposta;
    }
}