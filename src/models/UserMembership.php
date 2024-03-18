<?php

namespace Models;

use PDO;

class UserMembership extends AbstractModel
{
    protected string $table = 'user_memberships';
    protected array $fillable = [
        'user_id',
        'group_id'
    ];
    protected array $guarded = [];

    /**
     * Save the user's group membership.
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public function save(int $user_id, int $group_id): bool
    {
        $resp = false;
        $strFields = implode(', ', $this->fillable);
        if ($strFields) {
            $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:user_id, :group_id, :now)';
            $params = [
                ':user_id' => $user_id,
                ':group_id' => $group_id,
                ':now' => date('Y-m-d h:i:s', time())
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $resp = $stmt->execute($params);
        }
        return $resp;
    }

    /**
     * Loading data by user_id.
     * @param int $user_id
     * @return string
     */
    public function memberships(int $user_id): array
    {
        $query = 'SELECT `group_id` FROM ' . $this->table . ' WHERE `user_id` = :user_id';
        $params = [
            ':user_id' => $user_id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $groupIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $groupIds;
    }

    /**
     * Loading data by group_id.
     * @param int $group_id
     * @return string
     */
    public function users(int $group_id)
    {
        $query = 'SELECT `user_id` FROM ' . $this->table . ' WHERE `group_id` = :group_id';
        $params = [
            ':group_id' => $group_id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $usersIds = collect($stmt->fetchAll(PDO::FETCH_ASSOC))->map(fn($item) => $item['user_id'])->toArray();
        return $usersIds;
    }
}
