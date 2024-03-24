<?php

namespace Models;

class TempBlockedUsers extends AbstractModel
{
    protected string $table = 'temp_blocked_users';
    protected array $fillable = [
        'user_id'
    ];
    protected array $guarded = [];

    /**
     * Sets the user to temporarily blocked.
     * @param int $user_id
     * @return bool
     */
    public function save(int $user_id): bool
    {
        $resp = false;
        $strFields = implode(', ', $this->fillable);
        if ($strFields) {
            $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:user_id, :now)';
            $params = [
                ':user_id' => $user_id,
                ':now' => date('Y-m-d h:i:s', time())
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $resp = $stmt->execute($params);
        }
        return $resp;
    }

    /**
     * Removes a user from temporarily blocked.
     * @param int $user_id
     * @return bool
     */
    public function delete(int $user_id): bool
    {
        $resp = false;
        $query = 'DELETE FROM `' . $this->table . '` WHERE `user_id` = :user_id';
        $params = [
            ':user_id' => $user_id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $resp = $stmt->execute($params);
        return $resp;
    }
}
