
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

1. Instale o XAMPP.
2. Inicie os serviços Apache e MySQL.
3. Coloque a pasta do projeto dentro do diretório `htdocs`.
4. Crie o banco de dados `gerenciador_logistico` no MySQL.
5. Importe a estrutura das tabelas do projeto.
6. Configure os dados de conexão com o banco no arquivo `config/conexao.php`.
7. Acesse pelo navegador:

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

## Autoria

Projeto acadêmico desenvolvido para a disciplina de Fundamentos de Programação para Internet — UNIPAR.
