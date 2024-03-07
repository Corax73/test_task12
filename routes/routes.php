<?php

use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::group(['prefix' => 'api'], function () {
    SimpleRouter::get('/', function() {
        return 'Stub';
    })->name('main');
});

SimpleRouter::router()->loadRoutes();
