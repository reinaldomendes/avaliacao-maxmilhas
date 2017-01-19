<?php

$this->get('/', 'HomeController@index');

#$this->resource('images', 'ImagesController');

$this->get('/images', 'ImagesController@index');
$this->get('/images/create', 'ImagesController@create');
$this->get('/images/:id/edit', 'ImagesController@edit');
$this->post('/images', 'ImagesController@store');

$this->put('/images/:id', 'ImagesController@update');
$this->delete('/images/:id', 'ImagesController@destroy');
