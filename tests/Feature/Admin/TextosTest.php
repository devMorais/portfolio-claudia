<?php

namespace Tests\Feature\Admin;

use App\Models\SecaoHero;
use App\Models\SecaoSobre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TextosTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        SecaoHero::create(['titulo' => 'Titulo antigo', 'texto' => 'Texto antigo do hero']);
        SecaoSobre::create(['titulo' => 'Historia antiga', 'ato_1_titulo' => 'Ato um antigo']);

        $this->actingAs(User::forceCreate([
            'name' => 'Teste',
            'email' => 'teste@example.com',
            'password' => Hash::make('senha-de-teste'),
        ]));
    }

    private function dados(array $hero = [], array $sobre = []): array
    {
        return [
            'hero' => $hero + ['sobretitulo' => 'dev', 'titulo' => 'Novo titulo', 'texto' => 'Novo texto'],
            'sobre' => $sobre + ['titulo' => 'Nova historia', 'subtitulo' => 'Sub',
                'ato_1_titulo' => 'A1', 'ato_1_texto' => 'T1',
                'ato_2_titulo' => 'A2', 'ato_2_texto' => 'T2',
                'ato_3_titulo' => 'A3', 'ato_3_texto' => 'T3'],
        ];
    }

    public function test_carrega_o_conteudo_atual_do_banco_com_maxlength_e_csrf(): void
    {
        $this->get(route('admin.textos.edit'))
            ->assertOk()
            ->assertSee('Titulo antigo')
            ->assertSee('Texto antigo do hero')
            ->assertSee('Ato um antigo')
            ->assertSee('maxlength="50"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_salva_e_mostra_confirmacao(): void
    {
        $this->put(route('admin.textos.update'), $this->dados())
            ->assertRedirect(route('admin.textos.edit'))
            ->assertSessionHas('sucesso');

        $this->assertDatabaseHas('secao_hero', ['titulo' => 'Novo titulo']);
        $this->assertDatabaseHas('secao_sobre', ['ato_3_titulo' => 'A3']);

        $this->get(route('admin.textos.edit'))->assertSee('Textos salvos');
    }

    public function test_erro_de_validacao_nao_perde_o_digitado(): void
    {
        $this->from(route('admin.textos.edit'))
            ->put(route('admin.textos.update'), $this->dados(['titulo' => '', 'texto' => 'Meu texto digitado']))
            ->assertRedirect(route('admin.textos.edit'))
            ->assertSessionHasErrors(['hero.titulo' => 'Preencha o título da apresentação.'])
            ->assertSessionHasInput('hero.texto', 'Meu texto digitado');

        $this->assertDatabaseHas('secao_hero', ['titulo' => 'Titulo antigo']);
    }

    public function test_titulo_acima_do_limite_e_recusado(): void
    {
        $this->put(route('admin.textos.update'), $this->dados(['titulo' => str_repeat('a', 51)]))
            ->assertSessionHasErrors('hero.titulo');
    }
}