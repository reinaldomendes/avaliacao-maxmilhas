<?php

require __DIR__.'/helpers/functions.php';

error_reporting(E_ALL);
set_error_handler(function () {
  $errors = func_get_args();
  echo $errors[1].$errors[2].':'.$errors[3]; die;
});
set_exception_handler(function () {
  print_r(func_get_args());die;
});

use Rbm\Http\Router;
use Rbm\Http\Request;
use Rbm\Http\Response;

$request = new Request;
$response = new Response();
$routeFile = __DIR__.'/../app/Http/routes.php';

$router = new Router($request, $response);

try {
    $router->setRouteFiles([$routeFile])
    ->route()
    ->setControllersNamespace('\\App\\Http\\Controllers\\')
    ->dispatch()
    ->send();
} catch (\Exception $e) {
    echo $e;
}
