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

    public function __construct()
    {
        $this->connect = new Connect;
    }

    /**
     * Returns the name of the model table.
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
