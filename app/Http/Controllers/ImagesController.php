<?php

namespace App\Http\Controllers;

class ImagesController
{
    public function index($request, $response)
    {
        return 'hello img world';
    }
    public function create($request, $response)
    {
        return 'Hello create world';
    }
    public function edit($request, $response)
    {
        return 'Hello edit world';
    }
}
