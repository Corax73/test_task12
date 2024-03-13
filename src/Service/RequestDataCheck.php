<?php

namespace Service;

use PDO;

class RequestDataCheck extends AbstractService
{
    protected int $minLengthPassword = 8;

    /**
     * Checking the entered email for uniqueness.
     * @param string $email
     * @return bool
     */
    public function checkEmailUniqueness(string $email): bool
    {
        $query = "SELECT * FROM `users` WHERE email = :email";
        $params = [
            ':email' => $email
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Checks for the presence of characters other than English letters and numbers.
     * @param string $password
     * @return bool
     */
    public function checkingPassword(string $password): bool
    {
        $resp = false;
        $existForbiddenCharacters = false;
        $passwordLength = strlen($password);
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $existForbiddenCharacters = true;
        }
        if (!$existForbiddenCharacters && $passwordLength >= $this->minLengthPassword) {
            $resp = true;
        }
        return $resp;
    }

    /**
     * Checking the entered title for uniqueness.
     * @param string $title
     * @return bool
     */
    public function checkGroupTitleUniqueness(string $title): bool
    {
        $query = "SELECT * FROM `groups` WHERE title = :title";
        $params = [
            ':title' => $title
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            return false;
        } else {
            return true;
        }
    }
}
