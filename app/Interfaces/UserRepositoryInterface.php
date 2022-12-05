<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{
    public function getAllUsers();
    public function getUserById($userId);
    public function getUserByfilter($key,$value);
    public function deleteUser($userId);
    public function createUser(array $userDetails);
    public function updateUser($userId, array $userDetails);
}
