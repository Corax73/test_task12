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
    SimpleRouter::post('/create/{target}', [EntityController::class, 'create'])->name('createEntity');
    SimpleRouter::get('/entities/{target}/{offset?}', [EntityController::class, 'index'])->name('getListEntities');
    SimpleRouter::get('/groups/users/{group_id}', [GroupController::class, 'show'])->name('getGroupUsers');
    SimpleRouter::post('/service/{command}', [ServiceController::class, 'service'])->name('service');
    SimpleRouter::partialGroup('users', function () {
        SimpleRouter::get('/rights/{user_id}', [UserController::class, 'showUsersRights'])->name('getUserRights');
        SimpleRouter::get('/membership/{user_id}', [UserController::class, 'showUsersGroups'])->name('getUsersGroups');
        SimpleRouter::post('/membership/', [UserController::class, 'create'])->name('setUserGroupMembership');
        SimpleRouter::delete('/membership/{user_id}/{group_id}', [UserController::class, 'destroyUserMembership'])->name('destroyUserGroupMembership');
        SimpleRouter::post('/temp-blocked/', [UserController::class, 'setTempBlockedUsers'])->name('setTempBlockedUsers');
        SimpleRouter::delete('/temp-blocked/{user_id}', [UserController::class, 'destroyTemporaryBlockingUser'])->name('destroyTemporaryBlockingUser');
    });
    SimpleRouter::partialGroup('rights', function () {
        SimpleRouter::post('/groups/', [RightsController::class, 'create'])->name('setGroupRights');
        SimpleRouter::get('/groups/{group_id}', [RightsController::class, 'show'])->name('getGroupRights');
        SimpleRouter::delete('/groups/{group_id}/{right_name}', [RightsController::class, 'destroy'])->name('destroyGroupRights');
        SimpleRouter::post('/temp-blocked/', [RightsController::class, 'setTempBlockedRight'])->name('setTempBlockedRight');
        SimpleRouter::delete('/temp-blocked/{right_name}', [RightsController::class, 'destroyTemporaryBlocking'])->name('destroyTemporaryBlocking');
    });
});

SimpleRouter::router()->loadRoutes();
