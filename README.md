# GDS — Gerador de Sistemas (Backend)

[![DOI](https://zenodo.org/badge/DOI/10.5281/zenodo.20316548.svg)](https://doi.org/10.5281/zenodo.20316548)

Aplicação backend do **GDS — Gerador de Sistemas**, plataforma de *Model-Driven Development* desenvolvida como Trabalho de Conclusão de Curso em Sistemas de Informação no **Centro Universitário La Salle do Rio de Janeiro (Unilasalle-RJ)**, em 2022.

Este repositório contém a **API REST** que recebe as modelagens do frontend, autentica os usuários via JWT, persiste os diagramas objeto-relacionais e aciona o serviço de geração de código.

## Sobre o sistema GDS

O GDS recebe a modelagem objeto-relacional de um sistema (classes, atributos, relacionamentos) e gera automaticamente uma aplicação web full-stack com backend PHP/Symfony, API REST documentada, autenticação JWT, painel admin Sonata, container Docker e deploy automatizado em AWS EC2.

O sistema é composto por **quatro componentes**:

| Componente | Responsabilidade | Repositório |
|---|---|---|
| Frontend | Editor visual de diagramas (Vue 3 SPA) | [gerador-de-sistemas-front](https://github.com/alanazeredoguedes/gerador-de-sistemas-front) |
| **Backend (este repo)** | API REST, autenticação, persistência | — |
| Code Generator | Motor de geração de código e deploy | [gerador-de-sistemas-code-generator](https://github.com/alanazeredoguedes/gerador-de-sistemas-code-generator) |
| Base | Template-base Symfony para apps gerados | [gerador-de-sistemas-base](https://github.com/alanazeredoguedes/gerador-de-sistemas-base) |

## Arquitetura

```
┌─────────────┐        ┌─────────────┐         ┌──────────────────┐
│ FRONTEND    │  HTTPS │ BACKEND     │ aciona  │ CODE GENERATOR   │
│ Vue 3 SPA   │ ─────► │ (este repo) │ ──────► │ + AwsHelper      │
│             │  REST  │ + JWT       │         │ + GitHub deploy  │
└─────────────┘        └─────────────┘         └────────┬─────────┘
                                                        │ clona e
                                                        │ adapta
                                                        ▼
                                               ┌──────────────────┐
                                               │ BASE             │
                                               │ template Symfony │
                                               │ + bundles core   │
                                               └──────────────────┘
```

## Stack

- **PHP 8.1**
- **Symfony 6**
- **Doctrine ORM**
- **MySQL**
- **JWT** (lexik/jwt-authentication-bundle) para autenticação
- **Sonata Admin** para painel administrativo
- **Docker** + Docker Compose

## Entidades principais

- `User`, `Group` — gestão de usuários e grupos (UserBundle)
- `Application` — aplicação que o usuário deseja gerar
- `Diagram` — diagrama objeto-relacional vinculado a uma Application
- `Content` — conteúdo modelado dentro de um diagrama (classes, atributos, relacionamentos)
- `Framework`, `ProgrammingLanguage` — opções de geração disponíveis

## Como executar (local)

### Pré-requisitos

- Docker e Docker Compose
- Editor `.env.local` a partir de `.env.base` com seus segredos locais

### Instalação

```bash
cp .env.base .env.local
# editar .env.local: APP_SECRET, DATABASE_URL, JWT_PASSPHRASE etc.

docker compose build app
docker compose up -d
```

### Inicialização do banco

```bash
docker compose exec app bin/console doctrine:schema:update --force
docker compose exec app bin/console lexik:jwt:generate-keypair
docker compose exec app bin/console security:hash-password
```

A API ficará disponível em `http://localhost:9001`.

### Testes

```bash
docker compose exec app vendor/bin/phpunit
```

## Como citar

Se você usar este software em pesquisa, por favor cite-o:

> Guedes, A. A. (2022). *GDS — Gerador de Sistemas: Uma ferramenta de modelagem e geração de sistemas (Backend)*. Trabalho de Conclusão de Curso, Centro Universitário La Salle do Rio de Janeiro. DOI: 10.5281/zenodo.XXXXXXX

Veja também o arquivo [`CITATION.cff`](./CITATION.cff) para metadados estruturados.

## Sobre o TCC

- **Autor:** Alan Azeredo Guedes
- **Orientador:** Prof. Msc. Mario João Junior
- **Instituição:** Centro Universitário La Salle do Rio de Janeiro (Unilasalle-RJ)
- **Curso:** Sistemas de Informação
- **Defesa:** Dezembro de 2022

## Licença

Distribuído sob a licença **MIT**. Veja [`LICENSE`](./LICENSE) para detalhes.
