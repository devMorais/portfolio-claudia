<?php

/*
|--------------------------------------------------------------------------
| index.php de producao (plano B)
|--------------------------------------------------------------------------
| Usado SO quando o servidor nao segue link simbolico e o public_html/claudia
| precisa ser uma pasta de verdade. Ver DEPLOY.md, secao "Se o symlink nao
| funcionar".
|
| Diferenca para o public/index.php normal: os caminhos apontam para o app
| que mora FORA do public_html, e o usePublicPath avisa o Laravel onde a
| pasta publica realmente esta (senao asset() e storage:link erram o alvo).
*/

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Onde o projeto mora, fora da area publica
$app_base = __DIR__ . '/../../claudia_app';

if (file_exists($maintenance = $app_base . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $app_base . '/vendor/autoload.php';

/** @var Application $app */
$app = require_once $app_base . '/bootstrap/app.php';

// Esta pasta e a raiz publica, nao a public/ de dentro do projeto
$app->usePublicPath(__DIR__);

$app->handleRequest(Request::capture());
