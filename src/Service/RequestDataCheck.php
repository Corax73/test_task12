<?php

namespace Service;

use Models\GroupRights;
use Models\TempBlockedRights;
use Models\TempBlockedUsers;
use Models\UserMembership;
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

    /**
     * Checks the existence of an entity.
     * @param string $target
     * @return bool
     */
    public function checkEntityExist(string $target): bool
    {
        $resp = false;
        $className = 'Models\\' . ucfirst($target);
        if (class_exists($className)) {
            $resp = true;
        }
        return $resp;
    }

    /**
     * Checks whether a group has the right.
     * @param int $group_id
     * @param string $right_name
     * @return bool
     */
    public function checkGroupHasRight(int $group_id, string $right_name): bool
    {
        $groupRights = new GroupRights();
        $query = "SELECT id FROM `" . $groupRights->getTable() . "` WHERE group_id = :group_id AND right_name = :right_name";
        $params = [
            ':group_id' => $group_id,
            ':right_name' => $right_name
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checking whether a user is in a group.
     * @param int $group_id
     * @param int $user_id
     * @return bool
     */
    public function checkGroupHasUser(int $group_id, int $user_id): bool
    {
        $userMembership = new UserMembership();
        $query = "SELECT id FROM `" . $userMembership->getTable() . "` WHERE group_id = :group_id AND user_id = :user_id";
        $params = [
            ':group_id' => $group_id,
            ':user_id' => $user_id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks user blocking.
     * @param int $user_id
     * @return bool
     */
    public function checkUserBlock(int $user_id): bool
    {
        $tempBlockedUsers = new TempBlockedUsers();
        $query = "SELECT id FROM `" . $tempBlockedUsers->getTable() . "` WHERE user_id = :user_id";
        $params = [
            ':user_id' => $user_id
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks right blocking.
     * @param string $right_name
     * @return bool
     */
    public function checkRightBlock(string $right_name): bool
    {
        $tempBlockedRights = new TempBlockedRights();
        $query = "SELECT id FROM `" . $tempBlockedRights->getTable() . "` WHERE right_name = :right_name";
        $params = [
            ':right_name' => $right_name
        ];
        $stmt = $this->connect->connect(PATH_CONF)->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
