<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Entrar no painel &middot; {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/scss/app.scss'])
</head>
<body>

<div class="login">
    <div class="login__caixa">
        <h1 class="login__titulo">Entrar no painel</h1>
        <p class="login__texto">Area de edicao do portfolio.</p>

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf

            @if ($errors->any())
                <p class="aviso aviso--erro">{{ $errors->first() }}</p>
            @endif

            <div class="campo">
                <label for="email" class="campo__rotulo">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       class="campo__entrada @error('email') campo__entrada--erro @enderror"
                       required autofocus autocomplete="username">
            </div>

            <div class="campo">
                <label for="password" class="campo__rotulo">Senha</label>
                <input type="password" id="password" name="password"
                       class="campo__entrada" required autocomplete="current-password">
            </div>

            <div class="campo">
                <label class="campo__rotulo" style="font-weight: 400;">
                    <input type="checkbox" name="lembrar" value="1">
                    Continuar conectada
                </label>
            </div>

            <div class="campo">
                <button type="submit" class="btn btn--principal btn--bloco">Entrar</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
