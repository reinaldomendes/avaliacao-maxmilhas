<?php

require __DIR__.'/../../vendor/autoload.php';

 $dotenv = new Dotenv\Dotenv(__DIR__.'/../../');
 $dotenv->load();

 function db_connection()
 {
     static $pdo;
     $databaseConfig = include __DIR__.'/../../database/config/connection.php';

     $env = getenv('DB_TEST_CONNECTION');
     $dbParams = $databaseConfig[$env];
     $dsn = $dbParams['driver'].
       ':host='.$dbParams['host'].
       ((!empty($dbParams['port'])) ? (';port='.$dbParams['port']) : '').
       ';dbname='.$dbParams['schema'];
     $pdo = $pdo ? $pdo :  new \PDO($dsn, $dbParams['username'], $dbParams['password']);

     return $pdo;
 }
