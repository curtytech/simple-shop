# Bloco de Notas

## Campos


- [ ] Lista 1
- [x] Lista 2

> Citação

Ideias Dashboard 
Lista de Imoveis mais vistos

# Configurar Timezone
config/app.php
'timezone' => 'America/Sao_Paulo',

## Apagar Cache do Git na vendor
git rm -r --cached vendor/

# Comands

composer require filament/filament:"^3.3" -W

php artisan filament:install --panels

php artisan migrate

php artisan db:seed

### criar link simbólico
php artisan storage:link

### Ngrok
ngrok http 80 --host-header=cardapiovirtual


### Criar um link simbólico (symlink)
cd ~/domains/nome-do-dominio/public_html
ln -s /home/u233139548/domains/cardapiovirtual.space/storage/app/public storage

ln -s /home/u233139548/domains/you.tec.br/public_html/shop/storage/app/public storage




### Mercado Pago User Test
public key: APP_USR-363f0832-00ac-45ef-9be2-09cd05088f45
access token: APP_USR-7300805590293882-102309-942806e25252e2ab4eeefb6f17bd74d2-2653728447
webhook: 1b72ab987b7828101db2c6a88336efbc10c92f8ae3e2f5fccac0375c3356f52b
webhooksecret: 1b72ab987b7828101db2c6a88336efbc10c92f8ae3e2f5fccac0375c3356f52b

Vendedor
2653728447
TESTUSER5313515600938305424
gZUzvKmtLX

Comprador
2653725947
TESTUSER215043599712708334
9Esd62GW7O