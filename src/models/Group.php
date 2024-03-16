<?php

namespace Models;

class Group extends AbstractModel
{
    protected string $table = 'groups';
    protected array $fillable = [
        'title'
    ];

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
            $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:title, :now)';
            $params = [
                ':title' => $title,
                ':now' => date('Y-m-d h:i:s', time())
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $resp = $stmt->execute($params);
        }
        return $resp;
    }

    /**
     * Returns an array of its rights received from the Group model by ID, if available.
     * @param int $id
     * @return array
     */
    public function rights(int $id): array
    {
        $groupRights = new GroupRights();
        $rights = $groupRights->getRights($id);
        $resp = $rights ? $rights : [];
        return $resp;
    }
}
