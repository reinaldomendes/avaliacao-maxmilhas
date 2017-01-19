<?php

namespace App\Http\Controllers;

class HomeController
{
    public function index($request, $response)
    {
        $collection = di()->make('PhotoRepository')->getList([], ['id' => 'DESC']);

        return view('home.index')->with('collection', $collection);
    }
}
