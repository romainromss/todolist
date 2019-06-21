<?php


namespace AppBundle\DataFixtures;


use AppBundle\Entity\User;
use AppBundle\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $dataTasks = [
            [
                'title' => 'tache 1',
                'content' => 'Description de la tache 1',
                'user' => 'user5'
            ],
            [
                'title' => 'tache 2',
                'content' => 'Description de la tache 2',
                'user' => 'user4'
            ],
            [
                'title' => 'tache 3',
                'content' => 'Description de la tache 3',
                'user' => 'user2'
            ],
            [
                'title' => 'tache 4',
                'content' => 'Une tache créé par un utilisateur anonyme',
                'user' => 'anonymous'
            ]
        ];
        foreach ($dataTasks as $dataTask) {
            /** @var User $user */
            $user = ($this->hasReference($dataTask['user'])) ? $this->getReference($dataTask['user']) : null;
            $task = TaskFactory::create(
                $dataTask['title'],
                $dataTask['content'],
                $user
            );
            $manager->persist($task);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            UserFixtures::class
        );
    }
}
