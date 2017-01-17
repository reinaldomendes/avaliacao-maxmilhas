<?php


$di = di();
$di->bindSingleton('Request', function () {
  return new \Rbm\Http\Request(file_get_contents('php://input'));
});

$di->bind('Response', function () {
  return new \Rbm\Http\Response();
});

$di->bind('Router', function () use ($di) {
  $result = new \Rbm\Http\Router($di->make('Request'));
  $routeFiles = $di->getParam('router.config.route.files');
  $result->setRouteFiles($routeFiles);

  return $result;
});

/*
* Make a dispatcher class
*/
$di->bind('Dispatcher', function ($routeString, $routeMached) use ($di) {
  $result = new \Rbm\Http\Dispatcher($routeString, $routeMached);

  return $result->setRequest($di->make('Request'))
        ->setResponse($di->make('Response'));

});

/*View logic*/
$di->bind('ViewRenderer', function () {
  return new Rbm\View\Renderer();
});

$di->bind('View', function ($name) use ($di) {
    $result = new Rbm\View\View($name);

    $result->setLocatePaths($di->getParam('view.config.locate.paths'))
            ->setExtensions($di->getParam('view.config.extensions'))
            ->setRenderer(function ($view) use ($di) {
              return $di->make('ViewRenderer')->render($view);
            });

    return $result;
});

/*Database connection*/
$di->bindSingleton('PDO', function () {
  $databaseConfig = include __DIR__.'/../../database/config/connection.php';
  $env = getenv('DB_CONNECTION');
  $dbParams = $databaseConfig[$env];
  $dsn = $dbParams['driver'].
        ':host='.$dbParams['host'].
        ((!empty($dbParams['port'])) ? (';port='.$dbParams['port']) : '').
        ';dbname='.$dbParams['schema'];

    return new \PDO($dsn, $dbParams['username'], $dbParams['password']);

});

/*
* PhotoDao
*/
$di->bind('PhotoDao', function () use ($di) {
    return new App\Dao\PhotoDao($di->make('PDO'));
});

$di->bind('PhotoRepository', function () use ($di) {
    $repository = new \App\Repository\PhotoRepository($di->make('PhotoDao'), function ($values) use ($di) {
        return $di->make('Photo', [$values]);
    });

    return $repository;
});

$di->bind('Photo', \App\Models\Photo::class);
