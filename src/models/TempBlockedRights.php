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
            try {
                $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:right, :now)';
                $params = [
                    ':right' => $right,
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
     * Removes the right from temporary blocking.
     * @param string $right
     * @return bool
     */
    public function delete(string $right): bool
    {
        $resp = false;
        try {
            $query = 'DELETE FROM `' . $this->table . '` WHERE `right_name` = :right';
            $params = [
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
