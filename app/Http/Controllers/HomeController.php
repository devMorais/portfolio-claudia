<?php

namespace App\Http\Controllers;

use App\Models\Configuracao;
use App\Models\Formacao;
use App\Models\Habilidade;
use App\Models\Projeto;
use App\Models\Secao;
use App\Models\SecaoHero;
use App\Models\SecaoSobre;
use App\Models\Trajetoria;
use Illuminate\Contracts\View\View;

/**
 * Monta a home (pagina unica).
 *
 * Tudo o que aparece na tela sai daqui. Se um texto estiver escrito dentro
 * de um .blade.php, a Claudia nao consegue trocar pelo painel: entao todo
 * conteudo vem do banco e passa por este controller.
 */
class HomeController extends Controller
{
    public function index(): View
    {
        // Quais secoes estao ligadas no painel, na ordem definida la.
        // O Blade usa isto tanto para montar o menu quanto para decidir o que
        // renderizar, entao ligar/desligar uma secao nunca deixa link do menu
        // apontando para ancora que nao existe.
        $secoes = Secao::visiveis()->get()->keyBy('slug');

        $config = Configuracao::todas();

        return view('home', [
            'secoes' => $secoes,
            'config' => $config,
            'hero' => SecaoHero::first(),
            'sobre' => SecaoSobre::first(),
            'habilidades' => Habilidade::tecnicas()->get()->groupBy('categoria'),
            'habilidadesHumanas' => Habilidade::humanas()->get(),
            'trajetorias' => Trajetoria::orderBy('ordem')->get(),
            'formacoes' => Formacao::orderBy('ordem')->get(),
            'projetos' => Projeto::orderBy('ordem')->get(),
            'whatsappUrl' => $this->whatsappUrl($config),
            'jsonLd' => $this->jsonLd($config),
        ]);
    }

    /**
     * JSON-LD do tipo Person: e o que faz o Google entender que o site e
     * sobre uma profissional, e nao uma loja ou um blog.
     *
     * Montado aqui e nao no Blade de proposito: as chaves do schema.org
     * comecam com arroba ('@context', '@type') e o Blade tenta compilar
     * isso como diretiva, quebrando a pagina com ParseError.
     */
    private function jsonLd(array $config): string
    {
        $dados = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $config['nome_completo'] ?? null,
            'jobTitle' => $config['titulo_profissional'] ?? null,
            'url' => url('/'),
            'email' => $config['email'] ?? null,
            'address' => $config['cidade'] ?? null,
            'sameAs' => array_values(array_filter([
                $config['linkedin_url'] ?? null,
                $config['github_url'] ?? null,
                $config['instagram_url'] ?? null,
            ])),
        ]);

        return json_encode(
            $dados,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG,
        );
    }

    /**
     * Monta o link do WhatsApp a partir do numero salvo nas configuracoes.
     * Devolve null se o numero nao foi preenchido, para o Blade esconder o
     * botao em vez de mostrar link quebrado.
     */
    private function whatsappUrl(array $config): ?string
    {
        $numero = preg_replace('/\D/', '', $config['whatsapp'] ?? '');

        if (blank($numero)) {
            return null;
        }

        // Sem codigo do pais o wa.me nao abre a conversa.
        if (! str_starts_with($numero, '55')) {
            $numero = '55' . $numero;
        }

        $mensagem = rawurlencode('Oi! Vi o seu portfolio e queria conversar.');

        return "https://wa.me/{$numero}?text={$mensagem}";
    }
}
