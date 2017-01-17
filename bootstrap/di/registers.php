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

$di->bind('View', function ($name) use ($di) {
    $result = new Rbm\View\View($name);
    $result->setLocatePaths($di->getParam('view.config.locate.paths'))
            ->setExtensions($di->getParam('view.config.extensions'));

    return $result;
});

// return [
//   '\\Rbm\Http\\Router' => function () {
//
//   },
// ];

//
// public function dispatcher(){
//
// }

