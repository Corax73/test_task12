<?php

namespace Models;

use Models\Connect;

abstract class AbstractModel
{
    protected Connect $connect;

    public function __construct() {
        $this->connect = new Connect;
    }
}