<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjetoRequest;
use App\Models\Projeto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjetoController extends Controller
{
    /** Pasta (dentro do disco public) onde ficam as capturas de tela dos projetos. */
    private const PASTA = 'projetos';

    public function index(): View
    {
        return view('admin.projetos.index', [
            'itens' => Projeto::orderBy('ordem')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return $this->formulario(new Projeto());
    }

    public function store(ProjetoRequest $request): RedirectResponse
    {
        $dados = Arr::except($request->validated(), ['imagem', 'remover_imagem']);
        // Projeto novo entra no fim da lista. A Claudia ajusta com as setas.
        $dados['ordem'] = (int) Projeto::max('ordem') + 1;

        if ($request->hasFile('imagem')) {
            $dados['imagem_url'] = $this->guardarImagem($request->file('imagem'));
        }

        $projeto = Projeto::create($dados);

        return $this->voltar($projeto, "Projeto \"{$projeto->nome}\" criado.");
    }

    public function edit(Projeto $projeto): View
    {
        return $this->formulario($projeto);
    }

    public function update(ProjetoRequest $request, Projeto $projeto): RedirectResponse
    {
        $dados = Arr::except($request->validated(), ['imagem', 'remover_imagem']);

        if ($request->hasFile('imagem')) {
            $nova = $this->guardarImagem($request->file('imagem'));
            $this->apagarImagem($projeto->imagem_url);
            $dados['imagem_url'] = $nova;
        } elseif ($request->boolean('remover_imagem')) {
            $this->apagarImagem($projeto->imagem_url);
            $dados['imagem_url'] = null;
        }

        $projeto->update($dados);

        return $this->voltar($projeto, "Projeto \"{$projeto->nome}\" salvo.");
    }

    public function destroy(Projeto $projeto): RedirectResponse
    {
        $nome = $projeto->nome;

        $this->apagarImagem($projeto->imagem_url);
        $projeto->delete();

        return redirect()
            ->route('admin.projetos.index')
            ->with('sucesso', "Projeto \"{$nome}\" excluído.");
    }

    public function subir(Projeto $projeto): RedirectResponse
    {
        return $this->mover($projeto, -1);
    }

    public function descer(Projeto $projeto): RedirectResponse
    {
        return $this->mover($projeto, 1);
    }

    /**
     * Troca o projeto de lugar com o vizinho e renumera a lista inteira
     * (1, 2, 3...). Renumerar conserta ordem repetida ou com buraco,
     * entao a ordem nunca fica ambigua no site.
     */
    private function mover(Projeto $projeto, int $passo): RedirectResponse
    {
        DB::transaction(function () use ($projeto, $passo) {
            $ids = Projeto::orderBy('ordem')
                ->orderBy('id')
                ->pluck('id')
                ->all();

            $posicao = array_search($projeto->id, $ids);
            $destino = $posicao + $passo;

            if ($posicao !== false && isset($ids[$destino])) {
                [$ids[$posicao], $ids[$destino]] = [$ids[$destino], $ids[$posicao]];
            }

            foreach ($ids as $indice => $id) {
                Projeto::whereKey($id)->update(['ordem' => $indice + 1]);
            }
        });

        return $this->voltar($projeto);
    }

    /** Guarda o arquivo e devolve o endereco que o site publico usa no <img>. */
    private function guardarImagem(UploadedFile $arquivo): string
    {
        return '/storage/' . $arquivo->store(self::PASTA, 'public');
    }

    /**
     * Apaga o arquivo SO se ele estiver na pasta dos projetos. Uma imagem de
     * outro lugar (a foto do hero, por exemplo) nunca e tocada.
     */
    private function apagarImagem(?string $url): void
    {
        if ($url && str_starts_with($url, '/storage/' . self::PASTA . '/')) {
            Storage::disk('public')->delete(self::PASTA . '/' . basename($url));
        }
    }

    private function formulario(Projeto $projeto): View
    {
        return view('admin.projetos.form', [
            'projeto' => $projeto,
            'limites' => ProjetoRequest::LIMITES,
        ]);
    }

    /** Volta para a lista, ja rolando ate o projeto mexido (ajuda no celular). */
    private function voltar(Projeto $projeto, ?string $mensagem = null): RedirectResponse
    {
        $resposta = redirect()->to(route('admin.projetos.index') . '#p-' . $projeto->id);

        return $mensagem ? $resposta->with('sucesso', $mensagem) : $resposta;
    }
}