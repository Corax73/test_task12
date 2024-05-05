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
    protected array $guarded = [];

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
            try {
                $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:group_id, :right_name, :now)';
                $params = [
                    ':group_id' => $group_id,
                    ':right_name' => $right,
                    ':now' => date('Y-m-d h:i:s', time())
                ];
                $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
                $this->connect->connect(PATH_CONF)->beginTransaction();
                $resp = $stmt->execute($params);
                $this->connect->connect(PATH_CONF)->commit();
            } catch (\Exception $e) {
                if ($this->connect->connect(PATH_CONF)->inTransaction()) {
                    $this->connect->connect(PATH_CONF)->rollback();
                }
                throw $e;
            }
        }
        return $resp;
    }

    /**
     * Loading data by group_id.
     * @param int $group_id
     * @return array<int, string>
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

    /**
     * Removes a right in a group.
     * @param int $group_id
     * @param string $right
     * @return bool
     */
    public function delete(int $group_id, string $right): bool
    {
        $resp = false;
        try {
            $query = 'DELETE FROM `' . $this->table . '` WHERE `group_id` = :group_id AND `right_name` = :right';
            $params = [
                ':group_id' => $group_id,
                ':right' => $right
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $this->connect->connect(PATH_CONF)->beginTransaction();
            $stmt->execute($params);
            $resp = $stmt->rowCount() > 0 ? true : false;
            $this->connect->connect(PATH_CONF)->commit();
        } catch (\Exception $e) {
            if ($this->connect->connect(PATH_CONF)->inTransaction()) {
                $this->connect->connect(PATH_CONF)->rollback();
            }
            throw $e;
        }
        return $resp;
    }
}
