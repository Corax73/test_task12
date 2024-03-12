<?php

namespace Service;

use Models\Connect;

/**
 * @property Connect $connect
 */
abstract class AbstractService
{
    protected Connect $connect;

    public function __construct()
    {
        $this->connect = new Connect;
    }
}
