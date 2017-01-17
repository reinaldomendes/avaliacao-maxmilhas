<?php

return [
  'mysql' => [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST', 'localhost'),
    'port' => getenv('DB_PORT', '3306'),
    'schema' => getenv('DB_DATABASE', 'php_maxmilhas'),
    'username' => getenv('DB_USERNAME', 'MySqlUserName'),
    'password' => getenv('DB_PASSWORD', 'ForgeSecret'),
  ],
];
