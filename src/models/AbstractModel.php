<?php

namespace Models;

use Models\Connect;
use PDO;

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

    /**
     * Searches by model ID. Returns an array with data or false.
     * @param int $id
     * @return array | bool
     */
    public function find(int $id): array | bool
    {
        $query = 'SELECT * FROM `' . $this->table . '` WHERE `id` = :id';
        $params = [
            ':id' => $id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $resp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resp ? $resp : false;
    }
}
