/**
 * JavaScript do site publico.
 *
 * Tudo aqui e melhoria: se este arquivo nao carregar, o site continua
 * legivel e navegavel. Nenhum conteudo depende de JS para aparecer.
 */

// --- Header ganha fundo depois de rolar -----------------------------------
const header = document.querySelector('[data-header]');

if (header) {
    const marcarRolagem = () => {
        header.classList.toggle('header--rolado', window.scrollY > 40);
    };

    marcarRolagem();
    // passive: true avisa o navegador que nao vamos bloquear a rolagem
    window.addEventListener('scroll', marcarRolagem, { passive: true });
}

// --- Menu sanduiche do mobile --------------------------------------------
const botaoMenu = document.querySelector('[data-menu-botao]');
const nav = document.querySelector('[data-menu]');

if (botaoMenu && nav) {
    const fecharMenu = () => {
        nav.classList.remove('header__nav--aberto');
        botaoMenu.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('menu-aberto');
    };

    botaoMenu.addEventListener('click', () => {
        const aberto = nav.classList.toggle('header__nav--aberto');
        botaoMenu.setAttribute('aria-expanded', String(aberto));
        // Trava a rolagem do fundo enquanto o menu cobre a tela
        document.body.classList.toggle('menu-aberto', aberto);
    });

    // Fecha ao clicar num link, senao o menu fica aberto sobre a secao
    nav.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', fecharMenu);
    });

    // Fecha no Esc e ao voltar para o desktop
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') fecharMenu();
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 860) fecharMenu();
    });
}

// --- Animacao de entrada ao rolar ----------------------------------------
// O CSS deixa .revelar invisivel, mas a classe so e COLOCADA aqui. Se o
// IntersectionObserver nao existir, nada fica invisivel: o conteudo aparece
// normal. Nunca esconda conteudo por CSS contando com JS para revelar.
const alvos = document.querySelectorAll('[data-revelar]');

if (alvos.length && 'IntersectionObserver' in window) {
    alvos.forEach((el) => el.classList.add('revelar'));

    const observador = new IntersectionObserver(
        (entradas) => {
            entradas.forEach((entrada) => {
                if (!entrada.isIntersecting) return;

                entrada.target.classList.add('revelar--visivel');
                // Anima uma vez por elemento, nao a cada rolagem
                observador.unobserve(entrada.target);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
    );

    alvos.forEach((el) => observador.observe(el));
}

