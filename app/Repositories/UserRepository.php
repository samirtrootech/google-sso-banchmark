<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;


class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function getUserById($userid)
    {
        return User::findOrFail($userid);
    }

    public function getUserByfilter($key,$value)
    {
        return User::where($key, $value)->first();
    }

    public function deleteUser($userId)
    {
        return User::destroy($userId);
    }

    public function createUser(array $userDetails)
    {
        return User::create($userDetails);
    }

    public function updateUser($userId, array $userDetails)
    {
        return User::whereId($userId)->update($userDetails);
    }
}
