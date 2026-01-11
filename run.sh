#!/bin/bash

set -e

echo "🚀 Configurando ambiente Docker..."

export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

echo "🛑 Parando containers..."
docker compose down

echo "🔨 Buildando containers..."
USER_ID=$USER_ID GROUP_ID=$GROUP_ID docker compose build --no-cache

echo "🚀 Subindo containers..."
USER_ID=$USER_ID GROUP_ID=$GROUP_ID docker compose up -d

echo "⏳ Aguardando aplicação subir..."
sleep 3

echo "✅ Status dos containers:"
docker compose ps

echo ""
echo "🎉 Ambiente pronto!"
echo "🌐 Aplicação: http://localhost:8080"
