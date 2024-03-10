<?php

namespace Service;

use Models\Connect;
use PDO;

class RequestDataCheck
{
    protected Connect $connect;
    protected int $minLengthPassword = 8;

    public function __construct()
    {
        $this->connect = new Connect;
    }

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
}
