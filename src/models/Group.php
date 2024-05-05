<?php

namespace Models;

use PDO;

class Group extends AbstractModel
{
    protected string $table = 'groups';
    protected array $fillable = [
        'title'
    ];
    protected array $guarded = [];

    /**
     * Save group data.
     * @param string $title
     * @return bool
     */
    public function save(string $title): bool
    {
        $resp = false;
        $strFields = implode(', ', $this->fillable);
        if ($strFields) {
            try {
                $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:title, :now)';
                $params = [
                    ':title' => $title,
                    ':now' => date('Y-m-d h:i:s', time())
                ];
                $this->connect->connect(PATH_CONF)->beginTransaction();
                $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
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
     * Returns an array of its rights received from the Group model by ID, if available.
     * @param int $id
     * @return array<int, string>
     */
    public function rights(int $id): array
    {
        $groupRights = new GroupRights();
        $rights = $groupRights->getRights($id);
        $resp = $rights ? $rights : [];
        return $resp;
    }

    /**
     * Returns an array ID of group users.
     * @param int $group_id
     * @return array<int, int>
     */
    public function users(int $group_id): array
    {
        $userMembership = new UserMembership();
        $users = $userMembership->users($group_id);
        $resp = $users ? $users : [];
        return $resp;
    }

    /**
     * Deletes a group.
     * @param int $group_id
     * @return bool
     */
    public function delete(int $group_id): bool
    {
        $resp = false;
        try {
            $query = 'DELETE FROM `' . $this->table . '` WHERE `id` = :group_id';
            $params = [
                ':group_id' => $group_id,
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

    /**
     * Returns an array of group fields by its title.
     * @param string $title
     * @return array<int, mixed>
     */
    public function getByTitle(string $title): array
    {
        $query = 'SELECT id, ' . implode(', ', array_diff($this->fillable, $this->guarded)) . ',created_at FROM `' . $this->table . '` WHERE `title` = :title';
        $params = [
            ':title' => $title
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $resp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resp ? collect($resp)->flatten()->toArray() : [];
    }
}
