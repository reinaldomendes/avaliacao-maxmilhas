<?php

error_reporting(E_ALL);
set_error_handler(function () {
  $errors = func_get_args();
  echo $errors[1].$errors[2].':'.$errors[3]; die;
});
set_exception_handler(function () {
  print_r(func_get_args());die;
});

require __DIR__.'/../vendor/autoload.php';

/*
load .env file inside a root folder
*/
$dotenv = new Dotenv\Dotenv(__DIR__.'/../');
$dotenv->load();

require __DIR__.'/functions/helpers.php';
require __DIR__.'/di/registers.php';

/*Set di config parameters*/
$viewPath = __DIR__.'/../resources/views/';
$routeFile = __DIR__.'/../app/Http/routes.php';
di()->bindParam('view.config.locate.paths', [$viewPath])
    ->bindParam('view.config.extensions', ['phtml'])
    ->bindParam('router.config.route.files', [$routeFile]);
