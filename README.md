# Projeto Laravel REST API com Docker

Este é um projeto **Laravel 10** containerizado com **Docker**, que implementa uma **API REST** utilizando `apiResource` para operações de CRUD (Create, Read, Update, Delete e Show).  
O projeto inclui os seguintes serviços:

- **PHP 8.2** com FPM
- **Nginx** para servir a aplicação
- **PostgreSQL** como banco de dados
- **Redis** para cache e filas
- **RabbitMQ** para filas assíncronas

---

## Estrutura de serviços

O projeto é composto pelos seguintes containers:

| Serviço     | Container      | Função                                      |
|------------|----------------|--------------------------------------------|
| app        | laravel_app    | Aplicação Laravel + PHP 8.2                |
| webserver  | laravel_web    | Nginx                                       |
| db         | laravel_db     | PostgreSQL                                  |
| cache      | laravel_redis  | Redis                                       |
| queue      | laravel_rabbit | RabbitMQ                                    |

---

## Pré-requisitos

Antes de começar, você precisa ter instalado:

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Git](https://git-scm.com/)

> Certifique-se de ter a porta 8080 livre para Nginx, e as portas padrão do PostgreSQL, Redis e RabbitMQ desocupadas.

---

## Instalação e execução

1. Clone o repositório:

git clone <URL_DO_SEU_REPOSITORIO>
cd <NOME_DO_PROJETO>

markdown
Copiar código

2. Dê permissão para o script `run.sh`:

chmod +x run.sh

lua
Copiar código

3. Execute o script para buildar e subir os containers:

./run.sh

yaml
Copiar código

O `run.sh` faz o seguinte:

- Builda os containers Docker
- Sobe todos os serviços (Laravel, Nginx, PostgreSQL, Redis, RabbitMQ)
- Executa `composer install` e migrations automaticamente

---

## Acessando a API

Base URL: `http://localhost:8080/api`

Exemplos de endpoints REST usando `apiResource` para usuários:

| Método | Endpoint           | Função                  |
|--------|------------------|------------------------|
| GET    | /api/users        | Listar usuários        |
| POST   | /api/users        | Criar usuário          |
| GET    | /api/users/{id}   | Mostrar usuário        |
| PUT    | /api/users/{id}   | Atualizar usuário      |
| DELETE | /api/users/{id}   | Deletar usuário        |

---

## Acessando o container da aplicação

Para acessar o container `app`:

docker exec -it laravel_app bash

yaml
Copiar código

---

## Filas e Jobs

O Laravel está configurado para usar **RabbitMQ** como driver de filas.  
Para consumir a fila dentro do container `app`:

php artisan rabbit:consume

yaml
Copiar código

---

## Observações

- As migrations são executadas automaticamente pelo `run.sh`.
- Redis e RabbitMQ são utilizados para filas e cache, garantindo performance e processamento assíncrono.
- Certifique-se de que as portas necessárias não estão ocupadas para evitar conflitos.