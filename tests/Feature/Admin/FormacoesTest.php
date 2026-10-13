<?php

namespace Tests\Feature\Admin;

use App\Models\Formacao;
use App\Models\Secao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class FormacoesTest extends TestCase
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
    }

    private function formacao(string $titulo, int $ordem): Formacao
    {
        return Formacao::create(['titulo' => $titulo, 'ordem' => $ordem]);
    }

    /** Mesma ordem que o site publico usa (Formacao::orderBy('ordem')). */
    private function titulos(): array
    {
        return Formacao::orderBy('ordem')->orderBy('id')->pluck('titulo')->all();
    }

    private function dados(array $extra = []): array
    {
        return $extra + [
            'titulo' => 'Curso novo',
            'instituicao' => 'Curso livre',
            'ano' => '2026',
            'carga_horaria' => '40h',
            'certificado_url' => 'https://exemplo.com/certificado',
        ];
    }

    public function test_lista_mostra_as_formacoes_na_ordem(): void
    {
        $this->formacao('Primeira', 1);
        $this->formacao('Segunda', 2);

        $this->get(route('admin.formacoes.index'))
            ->assertOk()
            ->assertSeeInOrder(['Primeira', 'Segunda'])
            ->assertSee('data-confirma', false);
    }

    public function test_formulario_de_criar_abre_com_csrf(): void
    {
        $this->get(route('admin.formacoes.create'))
            ->assertOk()
            ->assertSee('Nova formação')
            ->assertSee('name="_token"', false);
    }

    public function test_cria_formacao_no_fim_da_lista(): void
    {
        $this->formacao('A', 1);
        $this->formacao('B', 2);

        $this->post(route('admin.formacoes.store'), $this->dados(['titulo' => 'C']))
            ->assertRedirect();

        $this->assertDatabaseHas('formacoes', ['titulo' => 'C', 'ordem' => 3]);
    }

    public function test_validacao_exige_titulo_e_link_valido_e_mantem_o_digitado(): void
    {
        $this->from(route('admin.formacoes.create'))
            ->post(route('admin.formacoes.store'), $this->dados([
                'titulo' => '',
                'certificado_url' => 'isto-nao-e-um-link',
                'instituicao' => 'Instituição que eu digitei',
            ]))
            ->assertRedirect(route('admin.formacoes.create'))
            ->assertSessionHasErrors(['titulo', 'certificado_url'])
            ->assertSessionHasInput('instituicao', 'Instituição que eu digitei');

        $this->assertDatabaseCount('formacoes', 0);
    }

    public function test_formulario_de_editar_vem_preenchido(): void
    {
        $formacao = $this->formacao('Curso de React', 1);

        $this->get(route('admin.formacoes.edit', $formacao))
            ->assertOk()
            ->assertSee('Editar formação')
            ->assertSee('Curso de React');
    }

    public function test_edita_uma_formacao_sem_mudar_a_ordem(): void
    {
        $formacao = $this->formacao('Antigo', 1);

        $this->put(route('admin.formacoes.update', $formacao), $this->dados(['titulo' => 'Renomeado']))
            ->assertRedirect();

        $this->assertDatabaseHas('formacoes', ['id' => $formacao->id, 'titulo' => 'Renomeado', 'ordem' => 1]);
    }

    public function test_exclui_uma_formacao(): void
    {
        $formacao = $this->formacao('Apagar', 1);

        $this->delete(route('admin.formacoes.destroy', $formacao))->assertRedirect();

        $this->assertDatabaseMissing('formacoes', ['id' => $formacao->id]);
    }

    public function test_sobe_e_desce_e_o_site_segue_a_nova_ordem(): void
    {
        $this->formacao('A', 1);
        $b = $this->formacao('B', 2);
        $this->formacao('C', 3);

        $this->post(route('admin.formacoes.subir', $b))->assertRedirect();
        $this->assertSame(['B', 'A', 'C'], $this->titulos());

        $this->post(route('admin.formacoes.descer', $b))->assertRedirect();
        $this->assertSame(['A', 'B', 'C'], $this->titulos());
    }

    public function test_primeiro_nao_sobe_e_ultimo_nao_desce(): void
    {
        $a = $this->formacao('A', 1);
        $c = $this->formacao('C', 2);

        $this->post(route('admin.formacoes.subir', $a))->assertRedirect();
        $this->post(route('admin.formacoes.descer', $c))->assertRedirect();

        $this->assertSame(['A', 'C'], $this->titulos());
    }

    public function test_ordem_repetida_e_consertada_ao_mover(): void
    {
        $this->formacao('A', 1);
        $b = $this->formacao('B', 1);

        $this->post(route('admin.formacoes.subir', $b))->assertRedirect();

        $this->assertSame(['B', 'A'], $this->titulos());
        $this->assertSame([1, 2], Formacao::orderBy('ordem')->pluck('ordem')->all());
    }

    public function test_site_mostra_link_do_certificado_so_quando_preenchido(): void
    {
        Secao::create([
            'slug' => 'trajetoria', 'nome' => 'Trajetoria', 'titulo_menu' => 'Trajetória',
            'visivel' => true, 'ordem' => 1,
        ]);

        Formacao::create(['titulo' => 'Com certificado', 'certificado_url' => 'https://exemplo.com/cert', 'ordem' => 1]);
        Formacao::create(['titulo' => 'Sem certificado', 'ordem' => 2]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('href="https://exemplo.com/cert"', false)
            ->assertSee('Sem certificado');
    }
}
