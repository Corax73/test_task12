<?php

use Controllers\AuthController;
use Controllers\EntityController;
use Controllers\RightsController;
use Controllers\UserController;
use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::group(['prefix' => 'api'], function () {
    SimpleRouter::post('/login', [AuthController::class, 'login'])->name('main');
    SimpleRouter::post('/registration', [AuthController::class, 'registration'])->name('registration');
    SimpleRouter::get('/users-rights/{id}', [UserController::class, 'showUsersRights'])->name('getUserRights');
    SimpleRouter::post('/create/{target}', [EntityController::class, 'create'])->name('createEntity');
    SimpleRouter::get('/entities/{target}/{offset?}', [EntityController::class, 'index'])->name('getListEntities');
    SimpleRouter::post('/rights/', [RightsController::class, 'create'])->name('setGroupRights');
    SimpleRouter::get('/rights/{id}', [RightsController::class, 'show'])->name('getGroupRights');
    SimpleRouter::post('/membership/', [UserController::class, 'create'])->name('setUserGroupMembership');
    SimpleRouter::get('/membership/{id}', [UserController::class, 'showUsersGroups'])->name('getUsersGroups');
});

SimpleRouter::router()->loadRoutes();
