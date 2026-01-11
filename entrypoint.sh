#!/bin/bash
set -e

if [ ! -d "vendor" ]; then
    echo "Pasta vendor não encontrada. Rodando composer install..."
    composer install --no-interaction --optimize-autoloader
fi

if [ -f "composer.lock" ]; then
    echo "Verificando atualizações do composer..."
    composer install --no-interaction --optimize-autoloader
fi

if ! composer show php-amqplib/php-amqplib >/dev/null 2>&1; then
    echo "📡 Instalando php-amqplib para RabbitMQ..."
    composer require php-amqplib/php-amqplib:^3.0 --no-interaction
fi

if [ ! -f ".env" ]; then
    echo "⚡ .env não encontrado. Criando a partir de .env.example..."
    cp .env.example .env
    echo "✅ .env criado!"
fi

if ! grep -q "APP_KEY=" .env || [ -z "$(grep APP_KEY .env | cut -d '=' -f2)" ]; then
    echo "🔑 Gerando APP_KEY..."
    php artisan key:generate
fi

echo "🗄 Executando migrations..."
php artisan migrate --force

exec "$@"
