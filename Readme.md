# Projeto Avaliação Maxmilhas

# instalação

* Copie o arquivo */.env.exapmple* para */.env* e configure o a conexão com o banco de dados
* Execute o arquivo inicial.sql  que pode ser encontrado em */database/sql*



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


## Aplicação
A galeria será exibida na home page.
