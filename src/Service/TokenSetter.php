<?php

namespace Service;

use Models\User;

class TokenSetter extends AbstractService
{
    /**
     * Sets the token to the line with the given email.
     * @return bool
     */
    public function setToken(string $email): bool
    {
        $resp = false;
        try {
            $query = 'UPDATE ' . (new User())->getTable() . ' SET `remember_token` = :remember_token';

            $query .= ' WHERE `email` = ' . "'" . $email . "'";

            $token = uniqid(explode('@', $email)[0]);
            $params = [
                ':remember_token' => $token
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
        return $resp;
    }
}
