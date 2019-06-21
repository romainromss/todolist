<?php


namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function getAllUsers(): array
    {
        return $this->createQueryBuilder('u')
            ->getQuery()
            ->setCacheable(true)
            ->getResult();
    }

    /**
     * @param  $id
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserById($id)
    {
        return $this->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setCacheable(true)
            ->getOneOrNullResult();
    }

    /**
     * @param string $username
     *
     * @return mixed
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername(string $username)
    {
        return $this->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter(
                'username',
                $username
            )
            ->getQuery()
            ->getOneOrNullResult();
    }
}
