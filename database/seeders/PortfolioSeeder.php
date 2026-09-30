<?php

namespace Database\Seeders;

use App\Models\Configuracao;
use App\Models\Formacao;
use App\Models\Habilidade;
use App\Models\Projeto;
use App\Models\Secao;
use App\Models\SecaoHero;
use App\Models\SecaoSobre;
use App\Models\Trajetoria;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Popula o banco com a ESTRUTURA do portfolio.
 *
 * Todo texto marcado com [PREENCHER] e rascunho: a estrutura esta pronta
 * e o site roda, mas o conteudo e da Claudia. Troque pelo texto real
 * (pelo painel ou aqui) e rode de novo: e updateOrCreate, nao duplica.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        $this->usuarioAdmin();
        $this->secoes();
        $this->hero();
        $this->sobre();
        $this->habilidades();
        $this->trajetoria();
        $this->formacoes();
        $this->projetos();
        $this->configuracoes();
    }

    /**
     * Usuario do painel. A senha NUNCA fica escrita aqui: vem do .env.
     * Sem ADMIN_PASSWORD definido, o seeder nao cria o usuario e avisa.
     */
    private function usuarioAdmin(): void
    {
        $email = env('ADMIN_EMAIL');
        $senha = env('ADMIN_PASSWORD');

        if (blank($email) || blank($senha)) {
            $this->command->warn('Usuario do painel NAO criado: defina ADMIN_EMAIL e ADMIN_PASSWORD no .env e rode de novo.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Claudia', 'password' => Hash::make($senha)],
        );

        $this->command->info("Usuario do painel pronto: {$email}");
    }

    /** As secoes da home, na ordem de renderizacao e com o rotulo do menu. */
    private function secoes(): void
    {
        $secoes = [
            ['slug' => 'hero',        'nome' => 'Apresentacao',   'titulo_menu' => 'Início',         'visivel' => true],
            ['slug' => 'sobre',       'nome' => 'Minha historia', 'titulo_menu' => 'Minha história', 'visivel' => true],
            ['slug' => 'habilidades', 'nome' => 'Habilidades',    'titulo_menu' => 'Habilidades',    'visivel' => true],
            ['slug' => 'trajetoria',  'nome' => 'Trajetoria',     'titulo_menu' => 'Trajetória',     'visivel' => true],
            ['slug' => 'projetos',    'nome' => 'Projetos',       'titulo_menu' => 'Projetos',       'visivel' => true],
            ['slug' => 'contato',     'nome' => 'Contato',        'titulo_menu' => 'Contato',        'visivel' => true],
        ];

        foreach ($secoes as $ordem => $secao) {
            Secao::updateOrCreate(
                ['slug' => $secao['slug']],
                $secao + ['ordem' => $ordem + 1],
            );
        }
    }

    private function hero(): void
    {
        SecaoHero::updateOrCreate(['id' => 1], [
            'sobretitulo' => 'desenvolvedora web',
            'titulo' => 'Ola, eu sou a Claudia',
            'texto' => '[PREENCHER] Duas ou tres linhas na primeira pessoa: o que voce faz hoje, '
                . 'de onde voce veio e o que voce esta procurando. Este e o texto mais lido do site '
                . 'inteiro. Quem abre o link le isto e decide se continua rolando.',
            'foto_url' => null,
            'cta_primario_label' => 'Baixar curriculo',
            'cta_primario_url' => '/curriculo.pdf',
            'cta_secundario_label' => 'Falar no WhatsApp',
            'cta_secundario_url' => null,
        ]);
    }

    private function sobre(): void
    {
        SecaoSobre::updateOrCreate(['id' => 1], [
            'titulo' => 'Da radiologia para a tecnologia',
            'subtitulo' => 'Como a minha primeira carreira virou a minha maior vantagem na segunda',
            'ato_1_titulo' => 'De onde eu vim',
            'ato_1_texto' => '[PREENCHER] O que voce fazia na radiologia, por quanto tempo, e o que '
                . 'esse trabalho exigia de voce todo dia. Seja concreta: exame, protocolo, paciente, turno.',
            'ato_2_titulo' => 'Por que eu mudei',
            'ato_2_texto' => '[PREENCHER] O que aconteceu que fez voce decidir migrar, e quando. '
                . 'Sem tom de desculpa e sem se diminuir: foi uma escolha, nao uma fuga.',
            'ato_3_titulo' => 'Onde eu estou agora',
            'ato_3_texto' => '[PREENCHER] O que voce ja construiu, com que tecnologias trabalha hoje '
                . 'e que tipo de vaga voce esta procurando. Termine dizendo o que voce quer.',
        ]);
    }

    private function habilidades(): void
    {
        // Tecnicas. Nivel honesto: a entrevista tecnica cobra o que esta escrito aqui.
        $tecnicas = [
            ['nome' => 'PHP',        'categoria' => 'Backend',     'nivel' => 'Intermediario', 'icone' => 'php'],
            ['nome' => 'Laravel',    'categoria' => 'Backend',     'nivel' => 'Intermediario', 'icone' => 'laravel'],
            ['nome' => 'MySQL',      'categoria' => 'Backend',     'nivel' => 'Intermediario', 'icone' => 'mysql'],
            ['nome' => 'HTML',       'categoria' => 'Frontend',    'nivel' => 'Avancado',      'icone' => 'html5'],
            ['nome' => 'CSS',        'categoria' => 'Frontend',    'nivel' => 'Intermediario', 'icone' => 'css3'],
            ['nome' => 'JavaScript', 'categoria' => 'Frontend',    'nivel' => 'Basico',        'icone' => 'javascript'],
            ['nome' => 'Git',        'categoria' => 'Ferramentas', 'nivel' => 'Intermediario', 'icone' => 'git'],
            ['nome' => 'Figma',      'categoria' => 'Ferramentas', 'nivel' => 'Basico',        'icone' => 'figma'],
        ];

        foreach ($tecnicas as $ordem => $item) {
            Habilidade::updateOrCreate(
                ['nome' => $item['nome'], 'tipo' => 'tecnica'],
                $item + ['tipo' => 'tecnica', 'ordem' => $ordem + 1],
            );
        }

        // Humanas: o que a radiologia ensinou e vale em desenvolvimento.
        // Esta lista e o diferencial do portfolio. Dev junior nenhum tem isso.
        $humanas = [
            [
                'nome' => 'Seguir protocolo sem atalho',
                'descricao' => '[PREENCHER] Em exame de imagem, pular etapa nao e agilidade, e risco. '
                    . 'Explique como isso virou disciplina de processo, teste e revisao no seu codigo.',
            ],
            [
                'nome' => 'Atencao a detalhe',
                'descricao' => '[PREENCHER] Voce foi treinada para enxergar numa imagem o que quase '
                    . 'ninguem ve. Ligue isso a achar bug e a ler mensagem de erro com calma.',
            ],
            [
                'nome' => 'Trabalhar sob pressao',
                'descricao' => '[PREENCHER] Plantao, urgencia, gente esperando. Compare com prazo '
                    . 'apertado e sistema fora do ar: voce ja sabe nao entrar em panico.',
            ],
            [
                'nome' => 'Falar com quem nao e da area',
                'descricao' => '[PREENCHER] Voce explicava procedimento para paciente nervoso. E a mesma '
                    . 'habilidade de traduzir problema tecnico para cliente e para equipe.',
            ],
        ];

        foreach ($humanas as $ordem => $item) {
            Habilidade::updateOrCreate(
                ['nome' => $item['nome'], 'tipo' => 'humana'],
                $item + ['tipo' => 'humana', 'ordem' => $ordem + 1],
            );
        }
    }

    private function trajetoria(): void
    {
        $itens = [
            [
                'periodo' => '[ANO]',
                'titulo' => '[PREENCHER] Tecnica em radiologia',
                'subtitulo' => '[PREENCHER] Nome do local',
                'descricao' => '[PREENCHER] O que voce fazia e o que aprendeu ali.',
                'area' => 'saude',
            ],
            [
                'periodo' => '[ANO]',
                'titulo' => '[PREENCHER] Comecei a estudar programacao',
                'subtitulo' => '[PREENCHER] Curso ou forma de estudo',
                'descricao' => '[PREENCHER] Como comecou e o que estudou primeiro.',
                'area' => 'tecnologia',
            ],
            [
                'periodo' => '[ANO]',
                'titulo' => '[PREENCHER] Primeiros projetos de verdade',
                'subtitulo' => '[PREENCHER] Onde',
                'descricao' => '[PREENCHER] O que voce entregou e com que tecnologia.',
                'area' => 'tecnologia',
            ],
        ];

        foreach ($itens as $ordem => $item) {
            Trajetoria::updateOrCreate(
                ['titulo' => $item['titulo']],
                $item + ['ordem' => $ordem + 1],
            );
        }
    }

    private function formacoes(): void
    {
        Formacao::updateOrCreate(
            ['titulo' => '[PREENCHER] Curso ou certificado'],
            [
                'instituicao' => '[PREENCHER] Instituicao',
                'ano' => '[ANO]',
                'carga_horaria' => null,
                'ordem' => 1,
            ],
        );
    }

    private function projetos(): void
    {
        // A secao mais importante do site: e a prova do que o resto afirma.
        // 3 projetos bem acabados valem mais que 8 pela metade.
        $projetos = [
            [
                'nome' => '[PREENCHER] Projeto 1',
                'resumo' => '[PREENCHER] Uma frase dizendo o que ele faz e para quem.',
                'papel' => 'projeto proprio',
                'tecnologias' => 'Laravel, MySQL, Blade',
                'destaque' => true,
            ],
            [
                'nome' => '[PREENCHER] Projeto 2',
                'resumo' => '[PREENCHER] Uma frase dizendo o que ele faz e para quem.',
                'papel' => 'projeto de curso',
                'tecnologias' => 'PHP, HTML, CSS',
            ],
            [
                'nome' => '[PREENCHER] Projeto 3',
                'resumo' => '[PREENCHER] Uma frase dizendo o que ele faz e para quem.',
                'papel' => 'contribuicao em projeto real',
                'tecnologias' => 'Angular, Laravel',
            ],
        ];

        foreach ($projetos as $ordem => $item) {
            Projeto::updateOrCreate(
                ['nome' => $item['nome']],
                $item + ['ordem' => $ordem + 1],
            );
        }
    }

    private function configuracoes(): void
    {
        $configs = [
            // geral
            ['chave' => 'nome_completo',       'grupo' => 'geral', 'rotulo' => 'Nome completo',       'valor' => '[PREENCHER] Nome completo'],
            ['chave' => 'titulo_profissional', 'grupo' => 'geral', 'rotulo' => 'Titulo profissional', 'valor' => 'Desenvolvedora web'],
            ['chave' => 'cidade',              'grupo' => 'geral', 'rotulo' => 'Cidade',              'valor' => '[PREENCHER] Cidade, UF'],
            ['chave' => 'email',               'grupo' => 'geral', 'rotulo' => 'E-mail de contato',   'valor' => '[PREENCHER] seu@email.com'],
            ['chave' => 'curriculo_url',       'grupo' => 'geral', 'rotulo' => 'Curriculo em PDF',    'valor' => '/curriculo.pdf'],
            // redes
            ['chave' => 'whatsapp',      'grupo' => 'redes', 'rotulo' => 'WhatsApp (so numeros, com DDD)', 'valor' => ''],
            ['chave' => 'linkedin_url',  'grupo' => 'redes', 'rotulo' => 'LinkedIn',  'valor' => ''],
            ['chave' => 'github_url',    'grupo' => 'redes', 'rotulo' => 'GitHub',    'valor' => 'https://github.com/marceline-mrq'],
            ['chave' => 'instagram_url', 'grupo' => 'redes', 'rotulo' => 'Instagram', 'valor' => ''],
            // seo
            ['chave' => 'seo_titulo',    'grupo' => 'seo', 'rotulo' => 'Titulo da pagina', 'valor' => '[PREENCHER] Nome - Desenvolvedora web'],
            ['chave' => 'seo_descricao', 'grupo' => 'seo', 'rotulo' => 'Descricao (meta description)', 'valor' => '[PREENCHER] Uma frase de ate 155 caracteres.'],
            ['chave' => 'seo_og_imagem', 'grupo' => 'seo', 'rotulo' => 'Imagem de compartilhamento (1200x630)', 'valor' => ''],
        ];

        foreach ($configs as $config) {
            Configuracao::updateOrCreate(['chave' => $config['chave']], $config);
        }
    }
}
