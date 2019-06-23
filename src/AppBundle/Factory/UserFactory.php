<?php


namespace AppBundle\Factory;

use AppBundle\Entity\User;

class UserFactory
{
    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param $roles
     *
     * @return User
     * @throws \Exception
     */
    public static function create(
        string $username,
        string $password,
        string $email,
        $roles
    ) {
        $user = new User(
            $username,
            $password,
            $email,
            $roles
        );
        return $user;
    }
}
