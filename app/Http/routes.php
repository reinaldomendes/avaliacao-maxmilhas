<?php


 $this->get('/', 'HomeController@index');

$this->resource('images', 'ImagesController');
// Route::resource('images', function ($request) {
//
// });

