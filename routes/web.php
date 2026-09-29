<?php

use App\Http\Controllers\Admin\AutenticacaoController;
use App\Http\Controllers\Admin\PainelController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas publicas
|--------------------------------------------------------------------------
| A home e uma pagina unica. Cada secao e uma ancora (#sobre, #projetos...)
| e o menu do header e montado a partir das secoes VISIVEIS no banco.
|
| Nao existe rota de contato: por decisao do projeto a secao Contato so
| aponta para e-mail, WhatsApp e LinkedIn, sem formulario.
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Painel administrativo
|--------------------------------------------------------------------------
| Autenticacao por sessao (a padrao do Laravel). Nao usa token nem API:
| o painel e Blade, roda no mesmo dominio, sessao resolve.
*/

Route::prefix('admin')->name('admin.')->group(function () {
    // Login: so para quem NAO esta logado
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AutenticacaoController::class, 'formulario'])->name('login');
        Route::post('/login', [AutenticacaoController::class, 'entrar'])
            ->middleware('throttle:5,1');   // travar tentativa de forca bruta
    });

    // Tudo daqui para baixo exige estar logado
    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AutenticacaoController::class, 'sair'])->name('logout');
        Route::get('/', [PainelController::class, 'index'])->name('painel');

        /*
         * ------------------------------------------------------------------
         * A FAZER (cards PC-13 a PC-17 no board do Avante):
         * as telas de edicao do painel. A estrutura de login, layout e menu
         * ja esta pronta; cada card abaixo e um CRUD para a Claudia montar.
         *
         *   Route::resource('projetos', ProjetoController::class);
         *   Route::resource('habilidades', HabilidadeController::class);
         *   Route::resource('trajetorias', TrajetoriaController::class);
         *   Route::resource('formacoes', FormacaoController::class);
         *   Route::get('/textos', ...)   // hero e minha historia
         *   Route::get('/configuracoes', ...) // redes, SEO, curriculo, secoes
         * ------------------------------------------------------------------
         */
    });
});
