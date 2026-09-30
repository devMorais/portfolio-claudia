# Deploy — claudia.dolen.com.br

Hospedagem compartilhada Hostinger, deploy manual por SSH.

**Nenhuma senha mora neste arquivo.** As credenciais ficam só no `.env` do
servidor, que não é versionado.

## Layout no servidor

O código do Laravel fica **fora** do `public_html`. Só a pasta `public/` é
exposta na web. Isso não é preciosismo: com o app inteiro dentro do
`public_html`, o `.env` fica acessível em `claudia.dolen.com.br/.env` e a senha
do banco vaza para quem digitar a URL.

```
/home/u846585591/domains/dolen.com.br/
├── claudia_app/              <- o projeto inteiro (fora da web)
│   ├── app/  bootstrap/  config/  database/  resources/  routes/
│   ├── storage/  vendor/
│   ├── .env                  <- credenciais, nunca versionado
│   └── public/               <- é ISTO que o subdomínio serve
└── public_html/
    └── claudia -> ../claudia_app/public    (symlink)
```

## Dados do ambiente

| | |
|---|---|
| Subdomínio | claudia.dolen.com.br |
| Document root | `/home/u846585591/domains/dolen.com.br/public_html/claudia` |
| Código | `/home/u846585591/domains/dolen.com.br/claudia_app` |
| Banco | `u846585591_claudia` |
| Usuário do banco | `u846585591_claudia` |
| PHP | 8.3 ou superior (Laravel 13 exige ^8.3) |

---

## Primeiro deploy

### 1. Conferir a versão do PHP do CLI

```bash
php -v
```

O CLI padrão da Hostinger costuma ser mais novo que o do site e isso já quebrou
dependências no projeto Dolen. Se não for 8.3 ou 8.4, use o caminho explícito
em todos os comandos abaixo:

```bash
/opt/alt/php83/usr/bin/php -v
```

### 2. Clonar o projeto fora do public_html

```bash
cd /home/u846585591/domains/dolen.com.br
git clone https://github.com/devMorais/portfolio-claudia.git claudia_app
cd claudia_app
```

### 3. Instalar as dependências

```bash
composer install --no-dev --optimize-autoloader
```

`--no-dev` deixa de fora o que só serve para desenvolvimento e deixa o deploy
mais leve.

### 4. Criar o .env

```bash
cp .env.example .env
nano .env
```

Ajuste:

```
APP_NAME="Claudia Marques da Silva"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://claudia.dolen.com.br

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u846585591_claudia
DB_USERNAME=u846585591_claudia
DB_PASSWORD=a-senha-criada-no-hpanel

# Usuário do painel da Claudia. O seeder lê daqui e NÃO cria usuário sem isto.
ADMIN_EMAIL=mrqclaudia27@gmail.com
ADMIN_PASSWORD=defina-uma-senha-e-passe-para-ela
```

`APP_DEBUG=false` não é frescura: com `true`, qualquer erro mostra caminhos
internos e trechos de configuração do servidor para qualquer visitante.

### 5. Gerar a chave e subir o banco

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

`--force` é obrigatório em produção — sem ele o artisan pede confirmação
interativa e o comando falha.

O seeder deve imprimir `Usuario do painel pronto: mrqclaudia27@gmail.com`.
Se disser que NÃO criou, falta `ADMIN_PASSWORD` no `.env`.

### 6. Compilar os assets

O `public/build` é gerado pelo Vite e não vai no Git. Veja se há Node no
servidor:

```bash
node -v && npm -v
```

**Se houver:**

```bash
npm ci
npm run build
```

**Se não houver:** compile na sua máquina (`npm run build`) e suba a pasta
`public/build` inteira para `claudia_app/public/build`, por SFTP ou pelo
Gerenciador de Arquivos do hPanel.

Sem esse passo o site abre **sem estilo nenhum** — parece quebrado, mas é só o
CSS que não existe.

### 7. Permissões

```bash
chmod -R 775 storage bootstrap/cache
```

Permissão errada aqui dá erro 500 sem nenhuma explicação na tela. É a segunda
causa mais comum de deploy de Laravel que não sobe.

### 8. Ligar o subdomínio ao public/ do app

```bash
cd /home/u846585591/domains/dolen.com.br/public_html
ln -s ../claudia_app/public claudia
ls -la claudia
```

O `ls` tem que mostrar `claudia -> ../claudia_app/public`.

Se o subdomínio já tiver criado uma pasta `claudia`, apague ela **antes**
(`rm -rf claudia`) — mas confira que está vazia, para não apagar nada de
outra coisa.

### 9. Link do storage

```bash
cd /home/u846585591/domains/dolen.com.br/claudia_app
php artisan storage:link
```

Sem isso, imagem enviada pelo painel salva mas não abre pelo navegador. É o
erro número um com upload em Laravel, e a mensagem não ajuda em nada.

### 10. Cache de produção

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Atenção: rode isto **depois** de o `.env` estar correto. Com o `.env` errado, o
cache congela a configuração errada e o site fica apontando para o banco errado
mesmo depois de você corrigir o arquivo.

### 11. Conferir

- https://claudia.dolen.com.br abre com estilo
- `https://claudia.dolen.com.br/.env` responde **403 ou 404** (se baixar o
  arquivo, o layout está errado — pare tudo e volte ao passo 8)
- A foto e o `/curriculo.pdf` abrem
- `/admin` pede login e entra com o e-mail e a senha do `.env`
- Cadeado de HTTPS sem aviso de conteúdo misto no console

---

## Se o symlink não funcionar

Alguns servidores não seguem link simbólico. Sintoma: 403 ou 404 na home.

Nesse caso, `public_html/claudia` vira uma pasta de verdade com o conteúdo do
`public/`, e o `index.php` aponta para fora:

```bash
cd /home/u846585591/domains/dolen.com.br
rm -f public_html/claudia
mkdir -p public_html/claudia
cp -r claudia_app/public/* claudia_app/public/.htaccess public_html/claudia/
cp claudia_app/deploy/index.php public_html/claudia/index.php
```

E o link do storage passa a ser feito na mão:

```bash
ln -s ../../claudia_app/storage/app/public public_html/claudia/storage
```

Nesse arranjo, **todo deploy novo exige copiar o `public/` de novo** — é por
isso que o symlink é o caminho preferido.

---

## Deploys seguintes

```bash
cd /home/u846585591/domains/dolen.com.br/claudia_app
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm run build            # ou subir o public/build da sua máquina
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Se o conteúdo do seeder mudou e você quer refletir no ar:

```bash
php artisan db:seed --force
```

É `updateOrCreate`: não duplica. Mas **sobrescreve** o que tiver sido editado
pelo painel. Depois que as telas de edição existirem (cards PC-04 a PC-08), o
seeder deixa de ser a forma de atualizar conteúdo.

---

## Regras

- O `.env` de produção **nunca** entra no Git
- Credenciais de produção ficam fora do repositório (padrão da casa:
  `DEPLOY.private.md`, não versionado)
- Só o Fernando mexe em produção
