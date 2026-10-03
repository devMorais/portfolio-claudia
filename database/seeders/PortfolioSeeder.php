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
            'sobretitulo' => 'desenvolvedora front-end',
            'titulo' => 'Olá, eu sou a Claudia',
            'texto' => 'Sou tecnóloga em Radiologia e estou migrando para a tecnologia, com foco em '
                . 'front-end, automação de testes e inteligência artificial. Hoje contribuo em projetos '
                . 'reais com Angular, Laravel, MySQL e Python, trabalhando em equipe com Scrum e Kanban. '
                . 'Estou em busca da minha primeira oportunidade como desenvolvedora.',
            'foto_url' => '/foto.jpeg',
            'cta_primario_label' => 'Baixar currículo',
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
            'ato_1_texto' => 'Me formei tecnóloga em Radiologia pela Anhanguera e fiz um ano e meio de estágio '
                . 'na área. Antes disso, trabalhei no atendimento de uma loja de móveis, presencial e online. '
                . 'Nos dois lugares aprendi a lidar com pessoas, seguir processos e resolver problema na hora.',
            'ato_2_titulo' => 'Por que eu mudei',
            'ato_2_texto' => 'Escolhi migrar para a tecnologia para entender mais a fundo essa área e poder '
                . 'aplicar o que aprendo também na saúde, que é de onde eu venho. Não foi uma fuga da '
                . 'radiologia: foi uma escolha de somar os dois mundos. Conhecer a rotina de quem trabalha '
                . 'na saúde me dá uma visão que ajuda a construir soluções que fazem sentido para essas pessoas.',
            'ato_3_titulo' => 'Onde eu estou agora',
            'ato_3_texto' => 'Contribuo em projetos de software da Dolen com Angular, Laravel, MySQL e Python, '
                . 'e uso IA como apoio para escrever e revisar código. Também estudo React e testes automatizados '
                . 'com Cypress, e cursei IA no SENAC-DF. Quero uma vaga onde eu possa crescer em front-end e QA, '
                . 'trabalhando em equipe, e levar tecnologia também para a área da saúde.',
        ]);
    }

    private function habilidades(): void
    {
        // Tecnicas. Nivel honesto: a entrevista tecnica cobra o que esta escrito aqui.
        $tecnicas = [
            ['nome' => 'Angular',    'categoria' => 'Frontend',    'nivel' => 'Basico',        'icone' => 'angular'],
            ['nome' => 'React',      'categoria' => 'Frontend',    'nivel' => 'Basico',        'icone' => 'react'],
            ['nome' => 'HTML',       'categoria' => 'Frontend',    'nivel' => 'Avancado',      'icone' => 'html5'],
            ['nome' => 'CSS',        'categoria' => 'Frontend',    'nivel' => 'Intermediario', 'icone' => 'css3'],
            ['nome' => 'JavaScript', 'categoria' => 'Frontend',    'nivel' => 'Basico',        'icone' => 'javascript'],
            ['nome' => 'PHP',        'categoria' => 'Backend',     'nivel' => 'Intermediario', 'icone' => 'php'],
            ['nome' => 'Laravel',    'categoria' => 'Backend',     'nivel' => 'Intermediario', 'icone' => 'laravel'],
            ['nome' => 'MySQL',      'categoria' => 'Backend',     'nivel' => 'Intermediario', 'icone' => 'mysql'],
            ['nome' => 'Python',     'categoria' => 'Backend',     'nivel' => 'Basico',        'icone' => 'python'],
            ['nome' => 'Cypress',    'categoria' => 'QA',          'nivel' => 'Basico',        'icone' => 'cypressio'],
            ['nome' => 'Git',        'categoria' => 'Ferramentas', 'nivel' => 'Intermediario', 'icone' => 'git'],
        ];

        foreach ($tecnicas as $ordem => $item) {
            Habilidade::updateOrCreate(
                ['nome' => $item['nome'], 'tipo' => 'tecnica'],
                $item + ['tipo' => 'tecnica', 'ordem' => $ordem + 1],
            );
        }

        // Humanas: o que a radiologia e o atendimento ensinaram e vale em desenvolvimento.
        $humanas = [
            [
                'nome' => 'Seguir protocolo sem atalho',
                'descricao' => 'Na radiologia, pular etapa não é agilidade, é risco. Levei essa disciplina para o '
                    . 'código: sigo o processo da equipe, reviso o que escrevo e testo antes de entregar.',
            ],
            [
                'nome' => 'Atenção a detalhe',
                'descricao' => 'Fui treinada para observar com cuidado e não deixar passar nada. No desenvolvimento, '
                    . 'isso me ajuda a achar bug e a ler mensagem de erro com calma.',
            ],
            [
                'nome' => 'Comunicação com quem não é da área',
                'descricao' => 'No atendimento e no estágio, eu explicava as coisas de forma clara para quem estava '
                    . 'do outro lado. É a mesma habilidade de traduzir um problema técnico para a equipe e para o cliente.',
            ],
            [
                'nome' => 'Resolver problema com agilidade',
                'descricao' => 'No comércio, o problema aparecia na frente do cliente e precisava de resposta rápida, '
                    . 'com organização e foco no resultado. Levo esse jeito para os prazos e para o trabalho em equipe.',
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
                'periodo' => '2022 - 2024',
                'titulo' => 'Tecnologia em Radiologia',
                'subtitulo' => 'Anhanguera',
                'descricao' => 'Graduação com um ano e meio de estágio na área, seguindo protocolos e '
                    . 'lidando com pacientes no dia a dia.',
                'area' => 'saude',
            ],
            [
                'periodo' => '2026 - atual',
                'titulo' => 'Contribuição em projetos da Dolen',
                'subtitulo' => 'Dolen',
                'descricao' => 'Participação em projetos de software com Angular, Laravel, MySQL e Python, '
                    . 'em equipe com Scrum e Kanban, usando IA como apoio na escrita e revisão de código.',
                'area' => 'tecnologia',
            ],
            [
                'periodo' => '2026',
                'titulo' => 'Inteligência Artificial',
                'subtitulo' => 'SENAC-DF, Faculdade de Tecnologia e Inovação (em andamento)',
                'descricao' => 'Formação em IA, com foco em aprendizado de máquina.',
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
        $formacoes = [
            [
                'titulo' => 'Tecnologia em Radiologia',
                'instituicao' => 'Anhanguera',
                'ano' => '2022 - 2024',
            ],
            [
                'titulo' => 'Inteligência Artificial',
                'instituicao' => 'SENAC-DF, Faculdade de Tecnologia e Inovação',
                'ano' => '2026 (em andamento)',
            ],
            [
                'titulo' => 'React, HTML, CSS e JavaScript',
                'instituicao' => 'Curso livre',
                'ano' => '-',
            ],
        ];

        foreach ($formacoes as $ordem => $item) {
            Formacao::updateOrCreate(
                ['titulo' => $item['titulo']],
                $item + ['carga_horaria' => null, 'ordem' => $ordem + 1],
            );
        }
    }

    private function projetos(): void
    {
        // Somente projetos reais, com repositorio publico.
        // Adicione novos aqui conforme forem publicados no GitHub.
        $projetos = [
            [
                'nome' => 'Portfólio em HTML e CSS',
                'resumo' => 'Meu primeiro portfólio, feito com HTML e CSS para apresentar meu currículo '
                    . 'e documentar minha transição de carreira.',
                'papel' => 'projeto proprio',
                'tecnologias' => 'HTML, CSS',
                'repositorio_url' => 'https://github.com/marceline-mrq/portfolio',
                'destaque' => true,
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
            ['chave' => 'nome_completo',       'grupo' => 'geral', 'rotulo' => 'Nome completo',       'valor' => 'Claudia Marques da Silva'],
            ['chave' => 'titulo_profissional', 'grupo' => 'geral', 'rotulo' => 'Titulo profissional', 'valor' => 'Desenvolvedora front-end'],
            ['chave' => 'cidade',              'grupo' => 'geral', 'rotulo' => 'Cidade',              'valor' => 'Brasília, DF'],
            ['chave' => 'email',               'grupo' => 'geral', 'rotulo' => 'E-mail de contato',   'valor' => 'mrqclaudia27@gmail.com'],
            ['chave' => 'curriculo_url',       'grupo' => 'geral', 'rotulo' => 'Curriculo em PDF',    'valor' => '/curriculo.pdf'],
            // redes
            ['chave' => 'whatsapp',      'grupo' => 'redes', 'rotulo' => 'WhatsApp (so numeros, com DDD)', 'valor' => '61991572752'],
            ['chave' => 'linkedin_url',  'grupo' => 'redes', 'rotulo' => 'LinkedIn',  'valor' => 'https://www.linkedin.com/in/claudia-marques-87873a247/'],
            ['chave' => 'github_url',    'grupo' => 'redes', 'rotulo' => 'GitHub',    'valor' => 'https://github.com/marceline-mrq'],
            ['chave' => 'instagram_url', 'grupo' => 'redes', 'rotulo' => 'Instagram', 'valor' => 'https://www.instagram.com/clamarques___1'],
            // seo
            ['chave' => 'seo_titulo',    'grupo' => 'seo', 'rotulo' => 'Titulo da pagina', 'valor' => 'Claudia Marques da Silva - Desenvolvedora front-end'],
            ['chave' => 'seo_descricao', 'grupo' => 'seo', 'rotulo' => 'Descricao (meta description)', 'valor' => 'Desenvolvedora front-end em transição da radiologia para a tecnologia. Angular, Laravel, React e testes com Cypress. Veja meus projetos.'],
            ['chave' => 'seo_og_imagem', 'grupo' => 'seo', 'rotulo' => 'Imagem de compartilhamento (1200x630)', 'valor' => ''],
        ];

        foreach ($configs as $config) {
            Configuracao::updateOrCreate(['chave' => $config['chave']], $config);
        }
    }
}