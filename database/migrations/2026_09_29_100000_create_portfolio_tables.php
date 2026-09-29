<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabelas de conteudo do portfolio.
 *
 * Regra do projeto: TODO texto que aparece no site publico vem daqui.
 * Nada de frase escrita na mao dentro de arquivo .blade.php — se estiver
 * no Blade, a Claudia nao consegue editar pelo painel.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Liga/desliga e ordena as secoes da home pelo painel.
        Schema::create('secoes', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();      // hero, sobre, habilidades, ...
            $table->string('nome');                // rotulo que aparece no painel
            $table->string('titulo_menu')->nullable(); // rotulo no menu do header
            $table->boolean('visivel')->default(true);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });

        // Secao 1 — apresentacao (linha unica)
        Schema::create('secao_hero', function (Blueprint $table) {
            $table->id();
            $table->string('sobretitulo')->nullable();   // ex.: "desenvolvedora web"
            $table->string('titulo');                    // ex.: "Ola, eu sou a Claudia"
            $table->text('texto')->nullable();           // 2 a 3 linhas de apresentacao
            $table->string('foto_url')->nullable();
            $table->string('cta_primario_label')->nullable();
            $table->string('cta_primario_url')->nullable();
            $table->string('cta_secundario_label')->nullable();
            $table->string('cta_secundario_url')->nullable();
            $table->timestamps();
        });

        // Secao 2 — a historia da migracao de carreira, em 3 atos (linha unica)
        Schema::create('secao_sobre', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('subtitulo')->nullable();
            $table->text('ato_1_titulo')->nullable();  // de onde eu vim (radiologia)
            $table->text('ato_1_texto')->nullable();
            $table->text('ato_2_titulo')->nullable();  // por que eu mudei
            $table->text('ato_2_texto')->nullable();
            $table->text('ato_3_titulo')->nullable();  // onde estou e para onde vou
            $table->text('ato_3_texto')->nullable();
            $table->timestamps();
        });

        // Habilidades. tipo = tecnica (Laravel, PHP...) ou humana (as que vem
        // da radiologia: protocolo, precisao, pressao, trato com paciente).
        Schema::create('habilidades', function (Blueprint $table) {
            $table->id();
            $table->string('tipo')->default('tecnica');
            $table->string('nome');
            $table->string('categoria')->nullable();   // Frontend, Backend, Ferramentas
            $table->string('nivel')->nullable();       // Basico, Intermediario, Avancado
            $table->text('descricao')->nullable();     // usado nas habilidades humanas
            $table->string('icone')->nullable();       // slug do devicon/simpleicons
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });

        // Linha do tempo da carreira. area = saude ou tecnologia (muda a cor).
        Schema::create('trajetorias', function (Blueprint $table) {
            $table->id();
            $table->string('periodo');                 // "2018 - 2023" ou "2024"
            $table->string('titulo');
            $table->string('subtitulo')->nullable();   // empresa ou instituicao
            $table->text('descricao')->nullable();
            $table->string('area')->default('tecnologia');
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });

        // Cursos e certificados
        Schema::create('formacoes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('instituicao')->nullable();
            $table->string('ano')->nullable();
            $table->string('carga_horaria')->nullable();
            $table->string('certificado_url')->nullable();
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });

        // Prova de trabalho — a secao que sustenta o resto do site
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('resumo')->nullable();       // 1 a 2 linhas
            $table->text('descricao')->nullable();
            $table->string('papel')->nullable();        // "projeto proprio", "contribuicao"
            $table->string('tecnologias')->nullable();  // separadas por virgula
            $table->string('imagem_url')->nullable();
            $table->string('repositorio_url')->nullable();
            $table->string('site_url')->nullable();
            $table->boolean('destaque')->default(false);
            $table->unsignedInteger('ordem')->default(0);
            $table->timestamps();
        });

        // Chave/valor: links de redes, SEO, caminho do curriculo
        Schema::create('configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('chave')->unique();
            $table->text('valor')->nullable();
            $table->string('grupo')->default('geral'); // geral|redes|seo
            $table->string('rotulo')->nullable();      // como aparece no painel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach ([
            'configuracoes', 'projetos', 'formacoes',
            'trajetorias', 'habilidades', 'secao_sobre', 'secao_hero', 'secoes',
        ] as $tabela) {
            Schema::dropIfExists($tabela);
        }
    }
};
