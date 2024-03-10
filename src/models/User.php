<?php

namespace Models;

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

    /**
     * Save user data.
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function save(string $email, string $password): bool
    {
        $query = 'INSERT INTO ' . $this->table . ' (email, password, created_at) VALUES (:email, :check_id, :now)';

        $password = password_hash($password, PASSWORD_DEFAULT);

        $params = [
            ':email' => $email,
            ':check_id' => $password,
            ':now' => date('Y-m-d h:i:s', time())
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        return $stmt->execute($params);
    }
}
