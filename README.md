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


2. Dê permissão para o script `run.sh`:

chmod +x run.sh


3. Execute o script para buildar e subir os containers:

./run.sh


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
| POST   | /api/authenticate | Autenticação de rotas  |

---

## Documentação das Rotas

Acesse a documentação completa e faça download da collection:  
[Documentação API + Collection](https://documenter.getpostman.com/view/48635147/2sBXVfirM2)

---

## Autenticação

Todas as rotas da API **necessitam de um Bearer Token** para acesso.  
O token é obtido fazendo login com as credenciais definidas no arquivo `.env`:

API_LOGIN=login

API_PASSWORD=password

Para autenticar:

1. Faça uma requisição `POST` para o endpoint `/authenticate` com os dados do `.env`.
2. Você receberá um **Bearer Token** na resposta.
3. Inclua o token no header de todas as requisições subsequentes:

Authorization: Bearer <SEU_TOKEN>

> Sem o token, a API retornará erro de autenticação.

---

## Filas e Jobs

O Laravel está configurado para usar **RabbitMQ** como driver de filas.  
Para consumir a fila, siga os passos:

1. Acesse o container da aplicação:

`docker exec -it laravel_app bash`

2. Execute o comando Artisan dentro do container:

`php artisan rabbit:consume`

---

## Redis

O Redis é utilizado para:
Armazenar o token de autenticação do usuário, Cachear informações do usuário pelo ID.

Como acessar os dados

1. Entre no container do Redis:

`docker exec -it laravel_redis bash`

2. Selecione o banco usado pela aplicação (geralmente 1):

`SELECT 1`

3. Abra o CLI do Redis:

`redis-cli`

4. Liste todas as chaves:

`KEYS *`

---

## Observações

- As migrations são executadas automaticamente pelo `run.sh`.
- Redis e RabbitMQ são utilizados para filas e cache, garantindo performance e processamento assíncrono.
- Certifique-se de que as portas necessárias não estão ocupadas para evitar conflitos.