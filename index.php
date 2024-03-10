<?php

use Pecee\SimpleRouter\SimpleRouter;

require_once  __DIR__ . '/vendor/autoload.php';
require_once 'config/const.php';
require_once __DIR__ . '/routes/routes.php';

try {
    SimpleRouter::start();
} catch (Exception $e) {
    var_dump($e);
    die();
    header('HTTP/1.0 404 Not Found');
}
