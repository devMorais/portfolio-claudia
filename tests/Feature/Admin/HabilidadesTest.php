<?php

namespace Tests\Feature\Admin;

use App\Models\Habilidade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HabilidadesTest extends TestCase
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

    private function tecnica(string $nome, int $ordem): Habilidade
    {
        return Habilidade::create(['tipo' => 'tecnica', 'nome' => $nome, 'ordem' => $ordem]);
    }

    private function humana(string $nome, int $ordem): Habilidade
    {
        return Habilidade::create(['tipo' => 'humana', 'nome' => $nome, 'ordem' => $ordem]);
    }

    /** Mesma ordem que o site publico usa (scopes tecnicas/humanas). */
    private function nomes(string $tipo): array
    {
        $consulta = $tipo === 'tecnica' ? Habilidade::tecnicas() : Habilidade::humanas();

        return $consulta->orderBy('id')->pluck('nome')->all();
    }

    public function test_lista_separa_tecnicas_e_humanas(): void
    {
        $this->tecnica('PHP', 1);
        $this->humana('Atencao a detalhe', 1);

        $this->get(route('admin.habilidades.index'))
            ->assertOk()
            ->assertSeeInOrder(['Habilidades técnicas', 'PHP', 'Habilidades humanas', 'Atencao a detalhe'])
            ->assertSee('data-confirma', false);
    }

    public function test_formulario_de_criar_abre_com_csrf(): void
    {
        $this->get(route('admin.habilidades.create'))
            ->assertOk()
            ->assertSee('Nova habilidade')
            ->assertSee('name="_token"', false);
    }

    public function test_cria_tecnica_no_fim_da_lista(): void
    {
        $this->tecnica('PHP', 1);
        $this->tecnica('Laravel', 2);

        $this->post(route('admin.habilidades.store'), [
            'nome' => 'Git', 'tipo' => 'tecnica', 'categoria' => 'Ferramentas',
            'nivel' => 'Basico', 'icone' => 'git',
        ])->assertRedirect();

        $this->assertDatabaseHas('habilidades', ['nome' => 'Git', 'tipo' => 'tecnica', 'ordem' => 3]);
    }

    public function test_humana_tem_ordem_propria(): void
    {
        $this->tecnica('PHP', 5);

        $this->post(route('admin.habilidades.store'), [
            'nome' => 'Protocolo', 'tipo' => 'humana', 'descricao' => 'Seguir o processo.',
        ])->assertRedirect();

        $this->assertDatabaseHas('habilidades', ['nome' => 'Protocolo', 'tipo' => 'humana', 'ordem' => 1]);
    }

    public function test_validacao_exige_nome_e_tipo_valido_e_mantem_o_digitado(): void
    {
        $this->from(route('admin.habilidades.create'))
            ->post(route('admin.habilidades.store'), [
                'nome' => '', 'tipo' => 'outro', 'descricao' => 'Texto que eu digitei',
            ])
            ->assertRedirect(route('admin.habilidades.create'))
            ->assertSessionHasErrors(['nome' => 'Preencha o nome da habilidade.', 'tipo'])
            ->assertSessionHasInput('descricao', 'Texto que eu digitei');

        $this->assertDatabaseCount('habilidades', 0);
    }

    public function test_nivel_fora_da_lista_e_recusado(): void
    {
        $this->post(route('admin.habilidades.store'), [
            'nome' => 'PHP', 'tipo' => 'tecnica', 'nivel' => 'Mestre',
        ])->assertSessionHasErrors('nivel');
    }

    public function test_formulario_de_editar_vem_preenchido(): void
    {
        $habilidade = $this->tecnica('Laravel', 1);

        $this->get(route('admin.habilidades.edit', $habilidade))
            ->assertOk()
            ->assertSee('Editar habilidade')
            ->assertSee('Laravel');
    }

    public function test_edita_uma_habilidade(): void
    {
        $habilidade = $this->tecnica('PHP', 1);

        $this->put(route('admin.habilidades.update', $habilidade), [
            'nome' => 'PHP 8', 'tipo' => 'tecnica', 'categoria' => 'Backend', 'nivel' => 'Intermediario',
        ])->assertRedirect();

        $this->assertDatabaseHas('habilidades', [
            'id' => $habilidade->id, 'nome' => 'PHP 8', 'categoria' => 'Backend', 'nivel' => 'Intermediario',
        ]);
    }

    public function test_trocar_de_tipo_manda_para_o_fim_da_outra_lista(): void
    {
        $habilidade = $this->tecnica('Comunicacao', 1);
        $this->humana('H1', 1);
        $this->humana('H2', 2);

        $this->put(route('admin.habilidades.update', $habilidade), [
            'nome' => 'Comunicacao', 'tipo' => 'humana',
        ])->assertRedirect();

        $this->assertDatabaseHas('habilidades', ['id' => $habilidade->id, 'tipo' => 'humana', 'ordem' => 3]);
    }

    public function test_exclui_uma_habilidade(): void
    {
        $habilidade = $this->tecnica('PHP', 1);

        $this->delete(route('admin.habilidades.destroy', $habilidade))->assertRedirect();

        $this->assertDatabaseMissing('habilidades', ['id' => $habilidade->id]);
    }

    public function test_sobe_e_desce_e_o_site_segue_a_nova_ordem(): void
    {
        $this->tecnica('A', 1);
        $b = $this->tecnica('B', 2);
        $this->tecnica('C', 3);

        $this->post(route('admin.habilidades.subir', $b))->assertRedirect();
        $this->assertSame(['B', 'A', 'C'], $this->nomes('tecnica'));

        $this->post(route('admin.habilidades.descer', $b))->assertRedirect();
        $this->assertSame(['A', 'B', 'C'], $this->nomes('tecnica'));
    }

    public function test_primeiro_nao_sobe_e_ultimo_nao_desce(): void
    {
        $a = $this->tecnica('A', 1);
        $c = $this->tecnica('C', 2);

        $this->post(route('admin.habilidades.subir', $a))->assertRedirect();
        $this->post(route('admin.habilidades.descer', $c))->assertRedirect();

        $this->assertSame(['A', 'C'], $this->nomes('tecnica'));
    }

    public function test_mover_nao_mexe_na_outra_lista(): void
    {
        $this->tecnica('A', 1);
        $b = $this->tecnica('B', 2);
        $this->humana('H1', 1);
        $this->humana('H2', 2);

        $this->post(route('admin.habilidades.subir', $b))->assertRedirect();

        $this->assertSame(['H1', 'H2'], $this->nomes('humana'));
    }

    public function test_ordem_repetida_e_consertada_ao_mover(): void
    {
        $this->tecnica('A', 1);
        $b = $this->tecnica('B', 1);

        $this->post(route('admin.habilidades.subir', $b))->assertRedirect();

        $this->assertSame(['B', 'A'], $this->nomes('tecnica'));
        $this->assertSame([1, 2], Habilidade::tecnicas()->pluck('ordem')->all());
    }
}