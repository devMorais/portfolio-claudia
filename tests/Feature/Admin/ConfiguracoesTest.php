<?php

namespace Tests\Feature\Admin;

use App\Models\Configuracao;
use App\Models\Secao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ConfiguracoesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->actingAs(User::forceCreate([
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'password' => Hash::make('senha-de-teste'),
        ]));

        Secao::create(['slug' => 'hero', 'nome' => 'Apresentacao', 'titulo_menu' => 'Início', 'visivel' => true, 'ordem' => 1]);
        Secao::create(['slug' => 'projetos', 'nome' => 'Projetos', 'titulo_menu' => 'Projetos', 'visivel' => true, 'ordem' => 2]);
    }

    private function dados(array $extra = []): array
    {
        return $extra + [
            'nome_completo' => 'Claudia Teste',
            'titulo_profissional' => 'Desenvolvedora front-end',
            'cidade' => 'Brasília, DF',
            'email' => 'claudia@example.com',
            'curriculo_url' => '/curriculo.pdf',
            'whatsapp' => '61999998888',
            'linkedin_url' => 'https://www.linkedin.com/in/exemplo',
            'github_url' => 'https://github.com/exemplo',
            'instagram_url' => 'https://www.instagram.com/exemplo',
            'seo_titulo' => 'Claudia - Front-end',
            'seo_descricao' => 'Uma descrição curta.',
            'seo_og_imagem' => '',
            'titulo_menu' => ['hero' => 'Início', 'projetos' => 'Projetos'],
            'secoes_visiveis' => ['hero' => '1', 'projetos' => '1'],
        ];
    }

    public function test_tela_abre_com_valores_do_banco_e_csrf(): void
    {
        Configuracao::create(['chave' => 'nome_completo', 'grupo' => 'geral', 'rotulo' => 'Nome completo', 'valor' => 'Claudia Do Banco']);

        $this->get(route('admin.configuracoes.edit'))
            ->assertOk()
            ->assertSee('Claudia Do Banco')
            ->assertSee('Seções do site')
            ->assertSee('name="_token"', false);
    }

    public function test_salva_os_campos_e_mostra_confirmacao(): void
    {
        $this->put(route('admin.configuracoes.update'), $this->dados(['email' => 'novo@example.com']))
            ->assertRedirect(route('admin.configuracoes.edit'))
            ->assertSessionHas('sucesso');

        $this->assertSame('novo@example.com', Configuracao::valor('email'));
        $this->assertSame('geral', Configuracao::firstWhere('chave', 'email')->grupo);
        $this->assertSame('https://github.com/exemplo', Configuracao::valor('github_url'));
        $this->assertSame('', Configuracao::valor('seo_og_imagem'));
    }

    public function test_desliga_secao_e_a_apresentacao_nunca_desliga(): void
    {
        // So a "hero" ligada, e nem ela vem marcada: o controller mantem ligada.
        $this->put(route('admin.configuracoes.update'), $this->dados([
            'secoes_visiveis' => [],
            'titulo_menu' => ['hero' => 'Começo', 'projetos' => 'Trabalhos'],
        ]))->assertRedirect();

        $hero = Secao::firstWhere('slug', 'hero');
        $projetos = Secao::firstWhere('slug', 'projetos');

        $this->assertTrue((bool) $hero->visivel);
        $this->assertFalse((bool) $projetos->visivel);
        $this->assertSame('Começo', $hero->titulo_menu);
        $this->assertSame('Trabalhos', $projetos->titulo_menu);
    }

    public function test_whatsapp_com_mascara_guarda_so_numeros(): void
    {
        $this->put(route('admin.configuracoes.update'), $this->dados(['whatsapp' => '(61) 99157-2752']))
            ->assertRedirect();

        $this->assertSame('61991572752', Configuracao::valor('whatsapp'));
    }

    public function test_validacao_recusa_email_e_link_ruins_e_mantem_o_digitado(): void
    {
        $this->from(route('admin.configuracoes.edit'))
            ->put(route('admin.configuracoes.update'), $this->dados([
                'email' => 'nao-e-email',
                'github_url' => 'isto-nao-e-um-link',
                'cidade' => 'Goiânia',
            ]))
            ->assertRedirect(route('admin.configuracoes.edit'))
            ->assertSessionHasErrors(['email', 'github_url'])
            ->assertSessionHasInput('cidade', 'Goiânia');

        $this->assertNull(Configuracao::valor('email'));
    }

    public function test_descricao_seo_acima_de_160_e_recusada(): void
    {
        $this->put(route('admin.configuracoes.update'), $this->dados([
            'seo_descricao' => str_repeat('a', 161),
        ]))->assertSessionHasErrors('seo_descricao');
    }

    public function test_nome_do_menu_vazio_e_recusado(): void
    {
        $this->put(route('admin.configuracoes.update'), $this->dados([
            'titulo_menu' => ['hero' => '', 'projetos' => 'Projetos'],
        ]))->assertSessionHasErrors('titulo_menu.hero');
    }
}