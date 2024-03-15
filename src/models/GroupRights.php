<?php

namespace Models;

use PDO;

class GroupRights extends AbstractModel
{
    protected string $table = 'group_rights';

    protected array $fillable = [
        'group_id',
        'right_name'
    ];

    /**
     * Save group rights.
     * @param int $group_id
     * @param string $right
     * @return bool
     */
    public function save(int $group_id, string $right): bool
    {
        $resp = false;
        $strFields = implode(', ', $this->fillable);
        if ($strFields) {
            $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:group_id, :right_name, :now)';
            $params = [
                ':group_id' => $group_id,
                ':right_name' => $right,
                ':now' => date('Y-m-d h:i:s', time())
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $resp = $stmt->execute($params);
        }
        return $resp;
    }

    /**
     * Loading data by group_id.
     * @param int $group_id
     * @return string
     */
    public function getRights(int $group_id): array
    {
        $query = 'SELECT `right_name` FROM ' . $this->table . ' WHERE `group_id` = :group_id';
        $params = [
            ':group_id' => $group_id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $rights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rights;
    }
}
