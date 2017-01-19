# Projeto Avaliação Maxmilhas



## instalação
* Copie o arquivo */.env.exapmple* para */.env* e configure o a conexão com o banco de dados
* Importe o arquivo *database/sql/inicial.sql* para o banco de dados



## Inicializando a aplicação.

```bash
  cd PROJECT_FOLDER
  composer install
  php -S localhost:8000 server.php
  ```


* Acesse no navegador http://localhost:8000



## Test Unit

* *x-debug* é necessário para execução dos testes
* Os logs "CodeCoverage" serão salvos na pasta /tmp/report
* É necessário configurar a conexão para a banco de teste no arquivo .env

```bash
  cd PROJECT_FOLDER
  phpunit
```

## componentes utilizados.
* vlucas/phpdotenv para carregar configurações dos arquivos .env
* bower - foi utilizado para instalar as libs(jQuery, Bootstrap)

## Aplicação
* A galeria será exibida na url '/' caso haja imagens cadastradas.



### Pastas e arquivos

* server.php  - Arquivo utilizado para execução do servidor imbutido do PHP.

#### app
  Contém as classes com a regra da aplicação.
  * app/Http/route.php # arquivo de rotas
  * app/Http/controllers # Diretório dos controllers
  * app/models/ # Logica de interação com o banco de dados

#### bootstrap
  Contém arquivos necessários para inicialização da aplicação.
  * bootstrap/app.php #contém a logica para execução das rotas e carregamento do controller
  * bootstrap/di/registers.php # Neste arquivo é realizado os "binds" para criação
            objetos através do dependency injection container.
  * bootstrap/functions/helpers.php # funções utilitárias.

#### database
    Arquivos relacionados ao banco de dados.
    * database/config/connection.php # conexções com o banco de dados
    * database/sql/ # arquivos sql
### lib
    Bibliotecas
    * lib/rbm-framework # Frameowrk MVC inspirado em Laravel, desenvolvido para esta avaliação.

### public
    Diretório público, caso utilize um servidor web como apache, esta é pasta que deveria ser exposta.
    * public/assets - arquivos css e js da aplicação
    * public/uploads - Local onde serão salvas as imagens
### resources  
    Recursos da aplicação, aqui se encontam as views, se tivesse utilizado
    pré-processadores css e js, os aquivos (scss,less,js) ficariam aqui também.
    * views - arquivos de layout e views da aplicação a extensão utilizada foi .phtml
### tests
    Pasta onde estão os testes unitários
## tmp
    Pasta de arquivos temporários.
