<?php

if (isset($this)) {
    $this->get('/', function () {
    return 'ok';
  });
}
