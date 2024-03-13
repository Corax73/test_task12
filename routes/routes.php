<?php

use Controllers\AuthController;
use Controllers\EntityController;
use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::group(['prefix' => 'api'], function () {
    SimpleRouter::post('/login', [AuthController::class, 'login'])->name('main');
    SimpleRouter::post('/registration', [AuthController::class, 'registration'])->name('registration');
    SimpleRouter::post('/create/{target}', [EntityController::class, 'create'])->name('create');
});

SimpleRouter::router()->loadRoutes();
