# Gerenciador Logístico

Sistema web desenvolvido para o gerenciamento de operações logísticas, como cadastro de produtos, endereços, transportadoras e controle de fretes.

Projeto desenvolvido como atividade prática da disciplina de Fundamentos de Programação para Internet — UNIPAR.

## Objetivo

Desenvolver uma aplicação web que permita organizar informações logísticas, facilitando o cadastro e o acompanhamento de fretes.

## Funcionalidades

- Cadastro e edição de produtos.
- Cadastro e edição de endereços de origem e destino.
- Cadastro e edição de transportadoras.
- Cadastro de fretes com múltiplos produtos e suas respectivas quantidades.
- Associação de fretes a transportadoras e endereços.
- Consulta de fretes por data.
- Geração de relatórios com opção de salvar em PDF.
- Painel inicial com informações gerais do sistema.

## Tecnologias utilizadas

- PHP
- MySQL
- HTML5
- CSS3
- JavaScript
- Bootstrap 5
- XAMPP
- Git e GitHub

## Estrutura do projeto

```text
gerenciador-logistico/
├── assets/
│   ├── css/
│   └── js/
├── config/
│   ├── conexao.example.php
│   └── conexao.php
├── database/
│   └── gerenciador_logistico.sql
├── enderecos/
├── fretes/
├── includes/
├── produtos/
├── relatorios/
├── transportadoras/
├── .gitignore
├── index.php
└── README.md
```

## Como executar o projeto

### 1. Preparar o ambiente

- Instale o [XAMPP](https://www.apachefriends.org/).
- Inicie os serviços Apache e MySQL.
- Baixe ou clone este repositório do GitHub.
- Coloque a pasta do projeto dentro do diretório `htdocs` do XAMPP.

### 2. Configurar o banco de dados

- Acesse o phpMyAdmin pelo XAMPP.
- Entre na opção **Importar**.
- Selecione o arquivo `database/gerenciador_logistico.sql`.
- Execute a importação.

O script criará automaticamente o banco de dados `gerenciador_logistico` e suas cinco tabelas.

### 3. Configurar a conexão

- Acesse a pasta `config`.
- Utilize o arquivo `conexao.example.php` como modelo.
- Crie uma cópia com o nome `conexao.php`.
- Configure os dados de conexão com o MySQL de acordo com seu ambiente local.

**Importante:** o arquivo `conexao.php` contém configurações locais e não deve ser enviado ao GitHub.

### 4. Acessar o sistema

Abra o navegador e acesse:

```text
http://localhost/gerenciador-logistico/
```

## Banco de dados

O sistema utiliza MySQL para armazenar informações sobre:

- Produtos.
- Endereços.
- Transportadoras.
- Fretes.
- Itens vinculados aos fretes.

O banco de dados possui cinco tabelas relacionadas, permitindo associar múltiplos produtos a um mesmo frete.

O script de criação do banco está disponível em:

`database/gerenciador_logistico.sql`

## Autoria

Projeto acadêmico desenvolvido para a disciplina de Fundamentos de Programação para Internet — UNIPAR.
