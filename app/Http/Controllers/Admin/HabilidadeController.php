<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\HabilidadeRequest;
use App\Models\Habilidade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HabilidadeController extends Controller
{
    public function index(): View
    {
        return view('admin.habilidades.index', [
            'tecnicas' => Habilidade::tecnicas()->orderBy('id')->get(),
            'humanas' => Habilidade::humanas()->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        $tipo = request('tipo') === 'humana' ? 'humana' : 'tecnica';

        return $this->formulario(new Habilidade(['tipo' => $tipo]));
    }

    public function store(HabilidadeRequest $request): RedirectResponse
    {
        $dados = $request->validated();
        // Item novo entra no fim da lista do tipo dele. A Claudia ajusta com as setas.
        $dados['ordem'] = $this->proximaOrdem($dados['tipo']);

        $habilidade = Habilidade::create($dados);

        return $this->voltar($habilidade, "Habilidade \"{$habilidade->nome}\" criada.");
    }

    public function edit(Habilidade $habilidade): View
    {
        return $this->formulario($habilidade);
    }

    public function update(HabilidadeRequest $request, Habilidade $habilidade): RedirectResponse
    {
        $dados = $request->validated();

        // Mudou de tipo: vai para o fim da lista do tipo novo.
        if ($dados['tipo'] !== $habilidade->tipo) {
            $dados['ordem'] = $this->proximaOrdem($dados['tipo']);
        }

        $habilidade->update($dados);

        return $this->voltar($habilidade, "Habilidade \"{$habilidade->nome}\" salva.");
    }

    public function destroy(Habilidade $habilidade): RedirectResponse
    {
        $nome = $habilidade->nome;
        $habilidade->delete();

        return redirect()
            ->route('admin.habilidades.index')
            ->with('sucesso', "Habilidade \"{$nome}\" excluída.");
    }

    public function subir(Habilidade $habilidade): RedirectResponse
    {
        return $this->mover($habilidade, -1);
    }

    public function descer(Habilidade $habilidade): RedirectResponse
    {
        return $this->mover($habilidade, 1);
    }

    /**
     * Troca o item de lugar com o vizinho do MESMO tipo e renumera a lista
     * inteira (1, 2, 3...). Renumerar conserta de quebra ordem repetida ou
     * com buraco, entao a ordem nunca fica ambigua no site.
     */
    private function mover(Habilidade $habilidade, int $passo): RedirectResponse
    {
        DB::transaction(function () use ($habilidade, $passo) {
            $ids = Habilidade::where('tipo', $habilidade->tipo)
                ->orderBy('ordem')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $posicao = array_search($habilidade->id, $ids);
            $destino = $posicao + $passo;

            if ($posicao !== false && isset($ids[$destino])) {
                [$ids[$posicao], $ids[$destino]] = [$ids[$destino], $ids[$posicao]];
            }

            foreach ($ids as $indice => $id) {
                Habilidade::whereKey($id)->update(['ordem' => $indice + 1]);
            }
        });

        return $this->voltar($habilidade);
    }

    private function formulario(Habilidade $habilidade): View
    {
        return view('admin.habilidades.form', [
            'habilidade' => $habilidade,
            'tipos' => HabilidadeRequest::TIPOS,
            'niveis' => HabilidadeRequest::NIVEIS,
            'limites' => HabilidadeRequest::LIMITES,
            'categorias' => Habilidade::whereNotNull('categoria')
                ->distinct()
                ->orderBy('categoria')
                ->pluck('categoria')
                ->all(),
        ]);
    }

    private function proximaOrdem(string $tipo): int
    {
        return (int) Habilidade::where('tipo', $tipo)->max('ordem') + 1;
    }

    /** Volta para a lista, ja rolando ate o item mexido (ajuda no celular). */
    private function voltar(Habilidade $habilidade, ?string $mensagem = null): RedirectResponse
    {
        $resposta = redirect()->to(route('admin.habilidades.index') . '#h-' . $habilidade->id);

        return $mensagem ? $resposta->with('sucesso', $mensagem) : $resposta;
    }
}