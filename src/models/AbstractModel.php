<?php

namespace Models;

use Connect;

class AbstractModel
{
    protected Connect $connect;

    public function __construct() {
        $this->connect = new Connect;
    }
}