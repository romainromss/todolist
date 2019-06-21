<?php


namespace AppBundle\Repository;


use AppBundle\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getAllTasks(): array
    {
        return $this->createQueryBuilder('t')
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
    public function getTaskById($id)
    {
        return $this->createQueryBuilder('t')
            ->where('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->setCacheable(true)
            ->getOneOrNullResult();
    }
}
