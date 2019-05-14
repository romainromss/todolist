<?php


namespace AppBundle\Factory;

use AppBundle\Entity\User;

class UserFactory
{
    /**
     * @param string $username
     * @param string $password
     * @param string $email
     * @param string $roles
     *
     * @return User
     */
    public function create(
        string $username,
        string $password,
        string $email,
        string $roles

    ){
        return new User($username, $password, $email, $roles);
    }
}
