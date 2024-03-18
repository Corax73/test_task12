<?php

namespace Models;

use PDO;

/**
 * @property string $table
 * @property array $fillable
 */
class User extends AbstractModel
{
    protected string $table = 'users';
    protected array $fillable = [
        'email',
        'password'
    ];
    protected array $guarded = ['password', 'remember_token'];

    /**
     * Save user data.
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function save(string $email, string $password): bool
    {
        $resp = false;
        $strFields = implode(', ', $this->fillable);
        if ($strFields) {
            $query = 'INSERT INTO `' . $this->table . '` (' . $strFields . ', created_at) VALUES (:email, :password, :now)';

            $password = password_hash($password, PASSWORD_DEFAULT);

            $params = [
                ':email' => $email,
                ':password' => $password,
                ':now' => date('Y-m-d h:i:s', time())
            ];
            $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
            $resp = $stmt->execute($params);
        }
        return $resp;
    }

    /**
     * User authentication check.
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function authUser(string $email, string $password): bool
    {
        $resp = false;
        $query = "SELECT * FROM `users` WHERE email = :email";
        $params = [
            ':email' => $email
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 1) {
            if (password_verify($password, $row[0]['password'])) {
                $resp = true;
            }
        }
        return $resp;
    }

    /**
     * Loading token of one user.
     * @param string $email
     * @return string
     */
    public function getToken(string $email): string
    {
        $query = 'SELECT `remember_token` FROM ' . $this->table . ' WHERE `email` = :email';
        $params = [
            ':email' => $email
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $token = $stmt->fetchAll(PDO::FETCH_ASSOC)[0]['remember_token'];
        return $token;
    }

    /**
     * Returns an array of user rights by his ID through a subquery of group membership.
     * @param int $id
     * @return array
     */
    public function getRights(int $id): array
    {
        $groupRights = new GroupRights();
        $userMembership = new UserMembership();
        $query = 'SELECT `right_name` FROM ' . $groupRights->getTable() . ' WHERE `group_id` = (SELECT `group_id` FROM ' . $userMembership->getTable()
            . ' WHERE `user_id` = :id LIMIT 1)';
        $params = [
            ':id' => $id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $resp = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $resp ? $resp : [];
    }
}
