# Portfólio da Claudia

Site pessoal para procurar vaga em desenvolvimento web, contando a história da
migração de carreira: **radiologia → tecnologia**.

A estrutura foi montada pelo Fernando. O que falta está no board
**Portfólio Claudia**, no Avante, nas cards PC-01 a PC-XX.

## Stack

- **Laravel 13** + Blade (sem framework de frontend separado)
- **MySQL** (`portfolio_claudia`)
- **Vite + SCSS próprio** (sem Bootstrap, sem Tailwind)
- Autenticação do painel por **sessão** (a padrão do Laravel, sem token/API)

## Como rodar

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate
```

Crie o banco `portfolio_claudia` no MySQL e ajuste o `.env`:

```
DB_DATABASE=portfolio_claudia
DB_USERNAME=root
DB_PASSWORD=

# Usuário do painel — o seeder lê daqui e NÃO cria usuário sem isto
ADMIN_EMAIL=seu@email.com
ADMIN_PASSWORD=escolha-uma-senha
```

Depois:

```bash
php artisan migrate --seed
npm run build       # ou: npm run dev, para recompilar enquanto edita
php artisan serve
```

- Site: http://127.0.0.1:8000
- Painel: http://127.0.0.1:8000/admin

## Como o conteúdo funciona

**Nenhum texto do site está escrito no Blade.** Tudo vem do banco e passa pelo
`HomeController`. Isso é o que permite editar o site pelo painel sem mexer em
código — se você escrever uma frase direto num `.blade.php`, quebra essa regra.

| Tabela | O que guarda |
|---|---|
| `secoes` | liga/desliga e ordena as seções da home |
| `secao_hero` | apresentação, foto, botões |
| `secao_sobre` | a história da migração, em 3 atos |
| `habilidades` | `tipo = tecnica` (PHP, Laravel...) ou `tipo = humana` (as trazidas da radiologia) |
| `trajetorias` | linha do tempo; `area = saude` ou `tecnologia` muda a cor |
| `formacoes` | cursos e certificados |
| `projetos` | prova de trabalho: imagem, tecnologias, links |
| `configuracoes` | redes sociais, currículo, SEO |

O seeder popula tudo com a estrutura pronta e o texto marcado `[PREENCHER]`.
Troque pelo texto real e rode `php artisan db:seed` de novo — é `updateOrCreate`,
não duplica.

## Onde mexer no visual

Toda cor, fonte, espaçamento e raio de borda está em
[`resources/scss/_tokens.scss`](resources/scss/_tokens.scss), como variável CSS.
Nenhum outro arquivo escreve `#hex` na mão. Para mudar a cara do site inteiro,
mexa só nesse arquivo.

## O que já está pronto

- Site público completo: header com menu montado das seções visíveis, hero,
  minha história, habilidades, trajetória, projetos, contato e footer
- Bandas de fundo alternadas calculadas sobre as seções **visíveis** (desligar
  uma seção do meio não deixa dois fundos iguais colados)
- Animação de entrada ao rolar, respeitando `prefers-reduced-motion`
- SEO: meta tags, Open Graph e JSON-LD do tipo `Person`
- Login do painel, guarda de rota, layout e tela inicial

## O que falta (cards no Avante)

As telas de edição do painel: textos, habilidades, trajetória, projetos e
configurações. O menu do painel já mostra esses itens apagados, com o número
da card em cada um.

## Regras do projeto

- Nunca commitar `.env` nem senha em código
- Nível de habilidade honesto: a entrevista técnica cobra o que está no site
- Todo projeto listado precisa de repositório público com README decente
