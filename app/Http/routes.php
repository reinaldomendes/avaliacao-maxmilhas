<?php


 $this->get('/', 'HomeController@index');

$this->get('/images/:id', function () {
  return 'image';
});
// Route::resource('images', function ($request) {
//
// });

