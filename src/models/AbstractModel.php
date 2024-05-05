<?php

namespace Models;

use Models\Connect;
use PDO;

/**
 * @property string $table
 * @property array $fillable;
 * @property array $guarded;
 * @property Connect $connect
 */
abstract class AbstractModel
{
    protected string $table;
    /**
     * @var array<string>
     */
    protected array $fillable;
     /**
     * @var array<string>
     */
    protected array $guarded;
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
     * @param int|array<int, int> $id
     * @return array<string, mixed> | bool
     */
    public function find(int|array $id): array | bool
    {
        if (is_array($id)) {
            $params = $id;
            $placeholders = str_repeat('?, ',  count($id) - 1) . '?';
            $query = "SELECT id, " . implode(', ', array_diff($this->fillable, $this->guarded)) . ",created_at FROM `$this->table` WHERE `id` IN ($placeholders)";
        } else {
            $query = 'SELECT id, ' . implode(', ', array_diff($this->fillable, $this->guarded)) . ',created_at FROM `' . $this->table . '` WHERE `id` = :id';
            $params = [
                ':id' => $id
            ];
        }
        $query .= ' ORDER BY `id` DESC';
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $resp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resp ? $resp : false;
    }

    /**
     * Returns an array of all model entries.
     * @param int $limit
     * @param int $offset
     * @return array<string, mixed>
     */
    public function all(int $limit = 12, int $offset = 0): array
    {
        $query = 'SELECT * FROM `' . $this->table . '` ORDER BY `id` DESC LIMIT :limit';
        if ($offset) {
            $query .= ' OFFSET :offset';
        }
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        if ($offset) {
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        $resp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resp ? $resp : [];
    }
}
