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

**Não rode `php artisan db:seed` depois do primeiro deploy.** O seeder
sobrescreve o que a Claudia editou pelo painel: textos, redes, cidade, nome
das seções, quais estão ligadas e as habilidades, trajetória e formações com
o mesmo nome. O conteúdo agora é atualizado só pelo painel.

---

## Imagens enviadas pelo painel

- Ficam em `claudia_app/storage/app/public/projetos`. Essa pasta não está
  no Git, então o `git pull` nunca as apaga. Não apague a pasta `storage`.
- O painel aceita JPG, PNG e WEBP até 2 MB. Se o PHP do servidor aceitar
  menos que isso, o envio falha lá mesmo funcionando no computador local.
  Confira no hPanel (PHP, Opções do PHP) os valores `upload_max_filesize` e
  `post_max_size`. Os dois precisam ser de pelo menos 2M.
- Se a imagem salva mas não abre, falta o `php artisan storage:link` (passo 9).

---