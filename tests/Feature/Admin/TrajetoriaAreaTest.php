<?php

namespace Tests\Feature\Admin;

use App\Models\Trajetoria;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TrajetoriaAreaTest extends TestCase
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

    private function dados(string $area): array
    {
        return [
            'periodo' => '2026',
            'titulo' => 'Item de teste',
            'area' => $area,
        ];
    }

    public function test_area_fora_de_saude_e_tecnologia_e_recusada(): void
    {
        $this->post(route('admin.trajetorias.store'), $this->dados('financas'))
            ->assertSessionHasErrors('area');

        $this->assertDatabaseCount('trajetorias', 0);
    }

    public function test_saude_e_tecnologia_sao_aceitas(): void
    {
        $this->post(route('admin.trajetorias.store'), $this->dados('saude'))->assertSessionHasNoErrors();
        $this->post(route('admin.trajetorias.store'), $this->dados('tecnologia'))->assertSessionHasNoErrors();

        $this->assertSame(['saude', 'tecnologia'], Trajetoria::orderBy('ordem')->pluck('area')->all());
    }
}