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
            try {
                $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:user_id, :group_id, :now)';
                $params = [
                    ':user_id' => $user_id,
                    ':group_id' => $group_id,
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
        $usersIds = collect($stmt->fetchAll(PDO::FETCH_ASSOC))->map(fn ($item) => $item['user_id'])->toArray();
        return $usersIds;
    }

    /**
     * Removes a user's membership in a group.
     * @param int $user_id
     * @param int $group_id
     * @return bool
     */
    public function delete(int $user_id, int $group_id): bool
    {
        $resp = false;
        try {
            $query = 'DELETE FROM `' . $this->table . '` WHERE `user_id` = :user_id AND `group_id` = :group_id';
            $params = [
                ':user_id' => $user_id,
                ':group_id' => $group_id
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
