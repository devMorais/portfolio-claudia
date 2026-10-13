<?php

namespace Tests\Feature\Admin;

use App\Models\Projeto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjetosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        Storage::fake('public');

        $this->actingAs(User::forceCreate([
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'password' => Hash::make('senha-de-teste'),
        ]));
    }

    private function projeto(string $nome, int $ordem, ?string $imagem = null): Projeto
    {
        return Projeto::create(['nome' => $nome, 'ordem' => $ordem, 'imagem_url' => $imagem]);
    }

    /** Mesma ordem que o site publico usa (Projeto::orderBy('ordem')). */
    private function nomes(): array
    {
        return Projeto::orderBy('ordem')->orderBy('id')->pluck('nome')->all();
    }

    private function dados(array $extra = []): array
    {
        return $extra + [
            'nome' => 'Novo projeto',
            'resumo' => 'Um resumo curto.',
            'papel' => 'projeto proprio',
            'tecnologias' => 'Laravel, MySQL',
            'repositorio_url' => 'https://github.com/exemplo/projeto',
            'site_url' => 'https://exemplo.com',
        ];
    }

    public function test_lista_mostra_os_projetos_na_ordem(): void
    {
        $this->projeto('Primeiro', 1);
        $this->projeto('Segundo', 2);

        $this->get(route('admin.projetos.index'))
            ->assertOk()
            ->assertSeeInOrder(['Primeiro', 'Segundo'])
            ->assertSee('data-confirma', false);
    }

    public function test_formulario_de_criar_abre_com_csrf_e_envio_de_arquivo(): void
    {
        $this->get(route('admin.projetos.create'))
            ->assertOk()
            ->assertSee('Novo projeto')
            ->assertSee('name="_token"', false)
            ->assertSee('multipart/form-data', false);
    }

    public function test_cria_projeto_sem_imagem_no_fim_da_lista(): void
    {
        $this->projeto('A', 1);
        $this->projeto('B', 2);

        $this->post(route('admin.projetos.store'), $this->dados(['nome' => 'C']))
            ->assertRedirect();

        $this->assertDatabaseHas('projetos', ['nome' => 'C', 'ordem' => 3, 'imagem_url' => null]);
    }

    public function test_cria_projeto_com_imagem(): void
    {
        $this->post(route('admin.projetos.store'), $this->dados([
            'imagem' => UploadedFile::fake()->image('capa.jpg', 1280, 800),
        ]))->assertRedirect();

        $projeto = Projeto::firstWhere('nome', 'Novo projeto');

        $this->assertStringStartsWith('/storage/projetos/', $projeto->imagem_url);
        Storage::disk('public')->assertExists('projetos/' . basename($projeto->imagem_url));
    }

    public function test_validacao_exige_nome_e_link_valido_e_mantem_o_digitado(): void
    {
        $this->from(route('admin.projetos.create'))
            ->post(route('admin.projetos.store'), $this->dados([
                'nome' => '',
                'repositorio_url' => 'isto-nao-e-um-link',
                'resumo' => 'Resumo que eu digitei',
            ]))
            ->assertRedirect(route('admin.projetos.create'))
            ->assertSessionHasErrors(['nome' => 'Preencha o nome do projeto.', 'repositorio_url'])
            ->assertSessionHasInput('resumo', 'Resumo que eu digitei');

        $this->assertDatabaseCount('projetos', 0);
    }

    public function test_arquivo_que_nao_e_imagem_e_recusado(): void
    {
        $this->post(route('admin.projetos.store'), $this->dados([
            'imagem' => UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf'),
        ]))->assertSessionHasErrors('imagem');

        $this->assertDatabaseCount('projetos', 0);
    }

    public function test_imagem_maior_que_2mb_e_recusada(): void
    {
        $this->post(route('admin.projetos.store'), $this->dados([
            'imagem' => UploadedFile::fake()->image('grande.jpg')->size(2500),
        ]))->assertSessionHasErrors('imagem');

        $this->assertDatabaseCount('projetos', 0);
    }

    public function test_formulario_de_editar_vem_preenchido(): void
    {
        $projeto = $this->projeto('Meu portfolio', 1);

        $this->get(route('admin.projetos.edit', $projeto))
            ->assertOk()
            ->assertSee('Editar projeto')
            ->assertSee('Meu portfolio');
    }

    public function test_edita_sem_mexer_na_imagem(): void
    {
        $projeto = $this->projeto('Antigo', 1, '/storage/projetos/atual.jpg');

        $this->put(route('admin.projetos.update', $projeto), $this->dados(['nome' => 'Renomeado']))
            ->assertRedirect();

        $this->assertDatabaseHas('projetos', [
            'id' => $projeto->id,
            'nome' => 'Renomeado',
            'imagem_url' => '/storage/projetos/atual.jpg',
            'ordem' => 1,
        ]);
    }

    public function test_trocar_a_imagem_apaga_a_antiga(): void
    {
        Storage::disk('public')->put('projetos/antiga.jpg', 'x');
        $projeto = $this->projeto('P', 1, '/storage/projetos/antiga.jpg');

        $this->put(route('admin.projetos.update', $projeto), $this->dados([
            'imagem' => UploadedFile::fake()->image('nova.png'),
        ]))->assertRedirect();

        Storage::disk('public')->assertMissing('projetos/antiga.jpg');

        $projeto->refresh();
        Storage::disk('public')->assertExists('projetos/' . basename($projeto->imagem_url));
    }

    public function test_remover_a_imagem_apaga_o_arquivo(): void
    {
        Storage::disk('public')->put('projetos/atual.jpg', 'x');
        $projeto = $this->projeto('P', 1, '/storage/projetos/atual.jpg');

        $this->put(route('admin.projetos.update', $projeto), $this->dados(['remover_imagem' => '1']))
            ->assertRedirect();

        Storage::disk('public')->assertMissing('projetos/atual.jpg');
        $this->assertDatabaseHas('projetos', ['id' => $projeto->id, 'imagem_url' => null]);
    }

    public function test_exclui_o_projeto_e_a_imagem(): void
    {
        Storage::disk('public')->put('projetos/atual.jpg', 'x');
        $projeto = $this->projeto('Apagar', 1, '/storage/projetos/atual.jpg');

        $this->delete(route('admin.projetos.destroy', $projeto))->assertRedirect();

        $this->assertDatabaseMissing('projetos', ['id' => $projeto->id]);
        Storage::disk('public')->assertMissing('projetos/atual.jpg');
    }

    public function test_excluir_nao_apaga_arquivo_de_fora_da_pasta_projetos(): void
    {
        Storage::disk('public')->put('outra/foto.jpg', 'x');
        $projeto = $this->projeto('Apagar', 1, '/storage/outra/foto.jpg');

        $this->delete(route('admin.projetos.destroy', $projeto))->assertRedirect();

        $this->assertDatabaseMissing('projetos', ['id' => $projeto->id]);
        Storage::disk('public')->assertExists('outra/foto.jpg');
    }

    public function test_sobe_e_desce_e_o_site_segue_a_nova_ordem(): void
    {
        $this->projeto('A', 1);
        $b = $this->projeto('B', 2);
        $this->projeto('C', 3);

        $this->post(route('admin.projetos.subir', $b))->assertRedirect();
        $this->assertSame(['B', 'A', 'C'], $this->nomes());

        $this->post(route('admin.projetos.descer', $b))->assertRedirect();
        $this->assertSame(['A', 'B', 'C'], $this->nomes());
    }

    public function test_primeiro_nao_sobe_e_ultimo_nao_desce(): void
    {
        $a = $this->projeto('A', 1);
        $c = $this->projeto('C', 2);

        $this->post(route('admin.projetos.subir', $a))->assertRedirect();
        $this->post(route('admin.projetos.descer', $c))->assertRedirect();

        $this->assertSame(['A', 'C'], $this->nomes());
    }

    public function test_ordem_repetida_e_consertada_ao_mover(): void
    {
        $this->projeto('A', 1);
        $b = $this->projeto('B', 1);

        $this->post(route('admin.projetos.subir', $b))->assertRedirect();

        $this->assertSame(['B', 'A'], $this->nomes());
        $this->assertSame([1, 2], Projeto::orderBy('ordem')->pluck('ordem')->all());
    }
}