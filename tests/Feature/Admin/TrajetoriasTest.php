<?php

namespace Tests\Feature\Admin;

use App\Models\Trajetoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrajetoriasTest extends TestCase
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

    private function item(string $titulo, int $ordem, string $area = 'tecnologia'): Trajetoria
    {
        return Trajetoria::create([
            'periodo' => '2026',
            'titulo' => $titulo,
            'area' => $area,
            'ordem' => $ordem,
        ]);
    }

    /** Mesma ordem que o site publico usa (Trajetoria::orderBy('ordem')). */
    private function titulos(): array
    {
        return Trajetoria::orderBy('ordem')->orderBy('id')->pluck('titulo')->all();
    }

    private function dados(array $extra = []): array
    {
        return $extra + [
            'periodo' => '2026 - atual',
            'titulo' => 'Novo item',
            'subtitulo' => 'Empresa',
            'descricao' => 'O que eu fiz.',
            'area' => 'tecnologia',
        ];
    }

    public function test_lista_mostra_os_itens_na_ordem(): void
    {
        $this->item('Primeiro', 1, 'saude');
        $this->item('Segundo', 2);

        $this->get(route('admin.trajetorias.index'))
            ->assertOk()
            ->assertSeeInOrder(['Primeiro', 'Segundo'])
            ->assertSee('data-confirma', false);
    }

    public function test_formulario_de_criar_abre_com_csrf(): void
    {
        $this->get(route('admin.trajetorias.create'))
            ->assertOk()
            ->assertSee('Novo item')
            ->assertSee('name="_token"', false);
    }

    public function test_cria_item_no_fim_da_linha_do_tempo(): void
    {
        $this->item('A', 1);
        $this->item('B', 2);

        $this->post(route('admin.trajetorias.store'), $this->dados(['titulo' => 'C']))
            ->assertRedirect();

        $this->assertDatabaseHas('trajetorias', ['titulo' => 'C', 'area' => 'tecnologia', 'ordem' => 3]);
    }

    public function test_validacao_exige_campos_e_mantem_o_digitado(): void
    {
        $this->from(route('admin.trajetorias.create'))
            ->post(route('admin.trajetorias.store'), $this->dados([
                'titulo' => '',
                'periodo' => '',
                'area' => 'outra',
                'descricao' => 'Texto que eu digitei',
            ]))
            ->assertRedirect(route('admin.trajetorias.create'))
            ->assertSessionHasErrors([
                'titulo' => 'Preencha o título.',
                'periodo' => 'Preencha o período.',
                'area' => 'A área precisa ser saúde ou tecnologia.',
            ])
            ->assertSessionHasInput('descricao', 'Texto que eu digitei');

        $this->assertDatabaseCount('trajetorias', 0);
    }

    public function test_titulo_acima_do_limite_e_recusado(): void
    {
        $this->post(route('admin.trajetorias.store'), $this->dados(['titulo' => str_repeat('a', 81)]))
            ->assertSessionHasErrors('titulo');
    }

    public function test_formulario_de_editar_vem_preenchido(): void
    {
        $item = $this->item('Radiologia', 1, 'saude');

        $this->get(route('admin.trajetorias.edit', $item))
            ->assertOk()
            ->assertSee('Editar item')
            ->assertSee('Radiologia');
    }

    public function test_edita_um_item(): void
    {
        $item = $this->item('Antigo', 1);

        $this->put(route('admin.trajetorias.update', $item), $this->dados([
            'titulo' => 'Novo titulo',
            'area' => 'saude',
        ]))->assertRedirect();

        $this->assertDatabaseHas('trajetorias', [
            'id' => $item->id,
            'titulo' => 'Novo titulo',
            'area' => 'saude',
            'ordem' => 1,
        ]);
    }

    public function test_exclui_um_item(): void
    {
        $item = $this->item('Apagar', 1);

        $this->delete(route('admin.trajetorias.destroy', $item))->assertRedirect();

        $this->assertDatabaseMissing('trajetorias', ['id' => $item->id]);
    }

    public function test_sobe_e_desce_e_o_site_segue_a_nova_ordem(): void
    {
        $this->item('A', 1);
        $b = $this->item('B', 2);
        $this->item('C', 3);

        $this->post(route('admin.trajetorias.subir', $b))->assertRedirect();
        $this->assertSame(['B', 'A', 'C'], $this->titulos());

        $this->post(route('admin.trajetorias.descer', $b))->assertRedirect();
        $this->assertSame(['A', 'B', 'C'], $this->titulos());
    }

    public function test_primeiro_nao_sobe_e_ultimo_nao_desce(): void
    {
        $a = $this->item('A', 1);
        $c = $this->item('C', 2);

        $this->post(route('admin.trajetorias.subir', $a))->assertRedirect();
        $this->post(route('admin.trajetorias.descer', $c))->assertRedirect();

        $this->assertSame(['A', 'C'], $this->titulos());
    }

    public function test_ordem_repetida_e_consertada_ao_mover(): void
    {
        $this->item('A', 1);
        $b = $this->item('B', 1);

        $this->post(route('admin.trajetorias.subir', $b))->assertRedirect();

        $this->assertSame(['B', 'A'], $this->titulos());
        $this->assertSame([1, 2], Trajetoria::orderBy('ordem')->pluck('ordem')->all());
    }
}
