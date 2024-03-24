<?php

use Controllers\AuthController;
use Controllers\EntityController;
use Controllers\GroupController;
use Controllers\RightsController;
use Controllers\ServiceController;
use Controllers\UserController;
use Pecee\SimpleRouter\SimpleRouter;

SimpleRouter::group(['prefix' => 'api'], function () {
    SimpleRouter::post('/login', [AuthController::class, 'login'])->name('main');
    SimpleRouter::post('/registration', [AuthController::class, 'registration'])->name('registration');
    SimpleRouter::get('/users/rights/{user_id}', [UserController::class, 'showUsersRights'])->name('getUserRights');
    SimpleRouter::get('/users/membership/{user_id}', [UserController::class, 'showUsersGroups'])->name('getUsersGroups');
    SimpleRouter::post('/users/membership/', [UserController::class, 'create'])->name('setUserGroupMembership');
    SimpleRouter::delete('/users/membership/{user_id}/{group_id}', [UserController::class, 'destroyUserMembership'])->name('destroyUserGroupMembership');
    SimpleRouter::post('/users/temp-blocked/', [UserController::class, 'setTempBlockedUsers'])->name('setTempBlockedUsers');
    SimpleRouter::delete('/users/temp-blocked/{user_id}', [UserController::class, 'destroyTemporaryBlockingUser'])->name('destroyTemporaryBlockingUser');
    SimpleRouter::post('/create/{target}', [EntityController::class, 'create'])->name('createEntity');
    SimpleRouter::get('/entities/{target}/{offset?}', [EntityController::class, 'index'])->name('getListEntities');
    SimpleRouter::post('/rights/groups/', [RightsController::class, 'create'])->name('setGroupRights');
    SimpleRouter::get('/rights/groups/{group_id}', [RightsController::class, 'show'])->name('getGroupRights');
    SimpleRouter::post('/rights/temp-blocked/', [RightsController::class, 'setTempBlockedRight'])->name('setTempBlockedRight');
    SimpleRouter::delete('/rights/temp-blocked/{right_name}', [RightsController::class, 'destroyTemporaryBlocking'])->name('destroyTemporaryBlocking');
    SimpleRouter::get('/groups/users/{group_id}', [GroupController::class, 'show'])->name('getGroupUsers');
    SimpleRouter::post('/service/{command}', [ServiceController::class, 'service'])->name('service');
});

SimpleRouter::router()->loadRoutes();
