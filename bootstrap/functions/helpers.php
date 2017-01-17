<?php

if (!function_exists('di')) {
    function di()
    {
        static $di;
        if (!$di) {
            $di = new \Rbm\Di\Di();
        }

        return $di;
    }
}
if (!function_exists('view')) {
    function view($name)
    {
        return di()->make('View', [$name]);
    }
}

//
// public function dispatcher(){
//
// }

