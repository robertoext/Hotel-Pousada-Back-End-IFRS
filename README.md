# Sistema de Gestão de Hotel/Pousada

Este projeto foi desenvolvido como parte das atividades do curso de Back End do IFRS.

A proposta é criar o backend de um sistema para auxiliar na organização de uma pousada, permitindo o controle de hóspedes, quartos, reservas, serviços adicionais e pagamentos. O projeto utiliza PostgreSQL, PHP, PDO, Programação Orientada a Objetos e operações CRUD.

## Estrutura do projeto

* `banco/01_criacao.sql`: cria as tabelas e os relacionamentos do banco de dados.
* `banco/02_dados.sql`: adiciona dados de exemplo para realizar os testes.
* `banco/03_consultas.sql`: contém as consultas solicitadas no exercício.
* `modelo/er.pdf`: apresenta o modelo entidade-relacionamento do sistema.
* `classes/`: contém as classes PHP do projeto.
* `config/`: contém a configuração da conexão com o PostgreSQL.

## Como executar o banco de dados

1. Crie um banco PostgreSQL chamado `hotel_pousada`.
2. Conecte-se ao banco criado.
3. Execute os arquivos nesta ordem:

```text
01_criacao.sql
02_dados.sql
03_consultas.sql
```

O primeiro arquivo cria as tabelas, o segundo insere os dados para testes e o terceiro apresenta as consultas do sistema.

## Funcionalidades do sistema

O sistema permitirá cadastrar, consultar, alterar e excluir informações relacionadas a:

* hóspedes;
* quartos;
* serviços;
* reservas.

Também serão controlados os pagamentos e os serviços utilizados em cada reserva.


Projeto desenvolvido por José Roberto.
