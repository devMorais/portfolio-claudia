<?php

use App\Http\Controllers\Admin\AutenticacaoController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Admin\FormacaoController;
use App\Http\Controllers\Admin\HabilidadeController;
use App\Http\Controllers\Admin\PainelController;
use App\Http\Controllers\Admin\ProjetoController;
use App\Http\Controllers\Admin\TextosController;
use App\Http\Controllers\Admin\TrajetoriaController;
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

        // Textos: Apresentacao (hero) e Minha historia (sobre)
        Route::get('/textos', [TextosController::class, 'edit'])->name('textos.edit');
        Route::put('/textos', [TextosController::class, 'update'])->name('textos.update');

        // Habilidades: tecnicas e humanas (mesma tabela, campo "tipo")
        Route::get('/habilidades', [HabilidadeController::class, 'index'])->name('habilidades.index');
        Route::get('/habilidades/nova', [HabilidadeController::class, 'create'])->name('habilidades.create');
        Route::post('/habilidades', [HabilidadeController::class, 'store'])->name('habilidades.store');
        Route::get('/habilidades/{habilidade}/editar', [HabilidadeController::class, 'edit'])
            ->whereNumber('habilidade')->name('habilidades.edit');
        Route::put('/habilidades/{habilidade}', [HabilidadeController::class, 'update'])
            ->whereNumber('habilidade')->name('habilidades.update');
        Route::delete('/habilidades/{habilidade}', [HabilidadeController::class, 'destroy'])
            ->whereNumber('habilidade')->name('habilidades.destroy');
        Route::post('/habilidades/{habilidade}/subir', [HabilidadeController::class, 'subir'])
            ->whereNumber('habilidade')->name('habilidades.subir');
        Route::post('/habilidades/{habilidade}/descer', [HabilidadeController::class, 'descer'])
            ->whereNumber('habilidade')->name('habilidades.descer');

        // Trajetoria: linha do tempo da carreira
        Route::get('/trajetorias', [TrajetoriaController::class, 'index'])->name('trajetorias.index');
        Route::get('/trajetorias/nova', [TrajetoriaController::class, 'create'])->name('trajetorias.create');
        Route::post('/trajetorias', [TrajetoriaController::class, 'store'])->name('trajetorias.store');
        Route::get('/trajetorias/{trajetoria}/editar', [TrajetoriaController::class, 'edit'])
            ->whereNumber('trajetoria')->name('trajetorias.edit');
        Route::put('/trajetorias/{trajetoria}', [TrajetoriaController::class, 'update'])
            ->whereNumber('trajetoria')->name('trajetorias.update');
        Route::delete('/trajetorias/{trajetoria}', [TrajetoriaController::class, 'destroy'])
            ->whereNumber('trajetoria')->name('trajetorias.destroy');
        Route::post('/trajetorias/{trajetoria}/subir', [TrajetoriaController::class, 'subir'])
            ->whereNumber('trajetoria')->name('trajetorias.subir');
        Route::post('/trajetorias/{trajetoria}/descer', [TrajetoriaController::class, 'descer'])
            ->whereNumber('trajetoria')->name('trajetorias.descer');

        // Formacoes: cursos e certificados
        Route::get('/formacoes', [FormacaoController::class, 'index'])->name('formacoes.index');
        Route::get('/formacoes/nova', [FormacaoController::class, 'create'])->name('formacoes.create');
        Route::post('/formacoes', [FormacaoController::class, 'store'])->name('formacoes.store');
        Route::get('/formacoes/{formacao}/editar', [FormacaoController::class, 'edit'])
            ->whereNumber('formacao')->name('formacoes.edit');
        Route::put('/formacoes/{formacao}', [FormacaoController::class, 'update'])
            ->whereNumber('formacao')->name('formacoes.update');
        Route::delete('/formacoes/{formacao}', [FormacaoController::class, 'destroy'])
            ->whereNumber('formacao')->name('formacoes.destroy');
        Route::post('/formacoes/{formacao}/subir', [FormacaoController::class, 'subir'])
            ->whereNumber('formacao')->name('formacoes.subir');
        Route::post('/formacoes/{formacao}/descer', [FormacaoController::class, 'descer'])
            ->whereNumber('formacao')->name('formacoes.descer');

        // Projetos: cartoes com captura de tela
        Route::get('/projetos', [ProjetoController::class, 'index'])->name('projetos.index');
        Route::get('/projetos/novo', [ProjetoController::class, 'create'])->name('projetos.create');
        Route::post('/projetos', [ProjetoController::class, 'store'])->name('projetos.store');
        Route::get('/projetos/{projeto}/editar', [ProjetoController::class, 'edit'])
            ->whereNumber('projeto')->name('projetos.edit');
        Route::put('/projetos/{projeto}', [ProjetoController::class, 'update'])
            ->whereNumber('projeto')->name('projetos.update');
        Route::delete('/projetos/{projeto}', [ProjetoController::class, 'destroy'])
            ->whereNumber('projeto')->name('projetos.destroy');
        Route::post('/projetos/{projeto}/subir', [ProjetoController::class, 'subir'])
            ->whereNumber('projeto')->name('projetos.subir');
        Route::post('/projetos/{projeto}/descer', [ProjetoController::class, 'descer'])
            ->whereNumber('projeto')->name('projetos.descer');

        // Configuracoes: dados gerais, redes, SEO e secoes liga/desliga
        Route::get('/configuracoes', [ConfiguracaoController::class, 'edit'])->name('configuracoes.edit');
        Route::put('/configuracoes', [ConfiguracaoController::class, 'update'])->name('configuracoes.update');
    });
});