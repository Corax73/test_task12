<?php

namespace Models;

use Models\Connect;

/**
 * @property string $table
 * @property Connect $connect
 */
abstract class AbstractModel
{
    protected string $table;
    protected Connect $connect;

    public function __construct() {
        $this->connect = new Connect;
    }
}