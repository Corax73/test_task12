<?php

namespace Models;

class TempBlockedRights extends AbstractModel
{
    protected string $table = 'temp_blocked';
    protected array $fillable = [
        'right_name'
    ];
    protected array $guarded = [];

    /**
     * Reserves the right to be blocked.
     * @param string $right
     * @return bool
     */
    public function save(string $right): bool
    {
        $resp = false;
        $strFields = implode(', ', $this->fillable);
        if ($strFields) {
            $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:right, :now)';
            $params = [
                ':right' => $right,
                ':now' => date('Y-m-d h:i:s', time())
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $resp = $stmt->execute($params);
        }
        return $resp;
    }

    /**
     * Removes the right from temporary blocking.
     * @param string $right
     * @return bool
     */
    public function delete(string $right): bool
    {
        $resp = false;
        $query = 'DELETE FROM `' . $this->table . '` WHERE `right_name` = :right';
        $params = [
            ':right' => $right
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $resp = $stmt->execute($params);
        return $resp;
    }
}
