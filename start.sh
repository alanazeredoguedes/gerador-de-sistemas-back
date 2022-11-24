#! /bin/bash

docker compose down

docker compose up -d

./run.sh composer install

# Gera chaves públicas/privadas para uso na aplicação - API Lexik
./run.sh bin/console lexik:jwt:generate-keypair

# Da permissão de escrita e leitura pasta /var/*
./run.sh chmod 777 /var/ -R

# Cria o banco de dados
#./run.sh bin/console doctrine:database:create

# Monta schema no banco de dados
#./run.sh bin/console doctrine:schema:update --force

# Cria usuário administrador
#./run.sh bin/console security:create-admin admin admin@email.com admin

# Cria usuário padrão
#./run.sh bin/console security:create-user user user@email.com user

# Faz instalação dos assets
./run.sh bin/console assets:install --symlink

# Limpa o cache do projeto
./run.sh bin/console cache:clear

#./run composer install

#./run bin/console assets:install --symlink

#./run bin/console cache:clear