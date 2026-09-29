<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Login e logout do painel.
 *
 * Autenticacao por sessao, a padrao do Laravel. Nao tem token nem API aqui:
 * o painel e Blade e roda no mesmo dominio do site, entao sessao resolve e
 * fica muito menos codigo para manter.
 */
class AutenticacaoController extends Controller
{
    public function formulario(): View
    {
        return view('admin.login');
    }

    public function entrar(Request $request): RedirectResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // O segundo argumento e o "continuar conectada" da tela de login.
        if (! Auth::attempt($credenciais, $request->boolean('lembrar'))) {
            // Mensagem generica de proposito: dizer "essa senha esta errada"
            // confirma para um estranho que o e-mail existe.
            throw ValidationException::withMessages([
                'email' => 'E-mail ou senha incorretos.',
            ]);
        }

        // Troca o ID da sessao depois do login. Sem isto, uma sessao capturada
        // antes do login continua valendo depois (fixacao de sessao).
        $request->session()->regenerate();

        return redirect()->intended(route('admin.painel'));
    }

    public function sair(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
