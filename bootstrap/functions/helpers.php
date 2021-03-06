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

if (!function_exists('request')) {
    function request()
    {
        return di()->make('Request', [$name]);
    }
}
if (!function_exists('session')) {
    function session()
    {
        return di()->make('Session');
    }
}

/*view helpers functions*/

if (!function_exists('upload_url')) {
    function upload_url($param = null)
    {
        return implode('/', ['/uploads', $param]);
    }
}
if (!function_exists('upload_path')) {
    function upload_path($param = null)
    {
        $uploadDir = realpath(__DIR__.'/../../public/uploads/');
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, '0777');
        }
        $result = implode('/', [$uploadDir, $param]);

        return $result;
    }
}

if (!function_exists('assets_url')) {
    function assets_url($param = null)
    {
        return implode('/', ['/assets', $param]);
    }
}

if (!function_exists('csrf_token')) {
    /*@todo implement it*/
    function csrf_token()
    {
        return;
    }
}
if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        $token = csrf_token();

        return "<input type='hidden' name='_csrf_token' value='{$token}' >";
    }
}

if (!function_exists('method_field')) {
    function method_field($method)
    {
        $method = strtoupper($method);

        return "<input type='hidden' name='_method' value='{$method}' >";
    }
}
