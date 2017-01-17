<?php

ob_start(); //init a buffer

require __DIR__.'/bootstrap.php';

$di = di();

$router = $di->make('Router');

try {
    $route = $router->route();
    if ($route) {
        $dispatcher = di()->make('Dispatcher', [key($route), current($route)]);

        $dispatcher
              ->setControllersNamespace('\\App\\Http\\Controllers\\')
              ->dispatch()
              ->send();
    } else {
        throw new \Exception('Route not found');
    }
} catch (\Exception $e) {
    echo $e;
}

ob_end_flush();
