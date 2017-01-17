<?php

namespace App\Http\Controllers;

class HomeController
{
    public function index($request, $response)
    {
        $repository = di()->make('PhotoRepository');
        $instance = $repository->find(1);
        $instance->path = 'nvovo';
        $repository->update($instance);

        $repository->delete($instance);
        // die;
        // die;
        $photoDao = di()->make('PhotoDao');
        $list = $photoDao->insert(['path' => 'lua', 'label' => 'fantastica', 'noceu' => 'tem pao']);

        return 'hello world';
    }
}
