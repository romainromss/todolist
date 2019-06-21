<?php

use AppBundle\Entity\AbstractEntity;
use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Factory\TaskFactory;
use AppBundle\Factory\UserFactory;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
 * Created by PhpStorm.
 * User: romss
 * Date: 2019-04-17
 * Time: 19:28
 */

class DoctrineContext implements Context
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var EncoderFactory
     */
    private $encoderFactory;

    /**
     * @var User
     */
    private $user2;
    /**
     * @var User
     */
    private $user7;
    /**
     * @var User
     */
    private $user10;

    /**
     * DoctrineContext constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param KernelInterface $kernel
     * @param EncoderFactory $encoderFactory
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        KernelInterface $kernel,
        EncoderFactory $encoderFactory
    ) {
        $this->entityManager = $entityManager;
        $this->encoderFactory = $encoderFactory;
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     *
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    public function clearDatabase()
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    /**
     * @Given /^I load the following user$/
     */
    public function iLoadTheFollowingUser(TableNode $table)
    {
        foreach ($table->getHash() as $hash) {
            $user = UserFactory::create(
                $hash['username'],
                $this->encoderFactory->getEncoder(User::class)->encodePassword($hash['password'], ''),
                $hash['email'],
                $hash['roles']
            );
            $this->entityManager->persist($user);
        }
        $this->entityManager->flush();
    }

    /**
     * @Given /^I load the following task$/
     */
    public function iLoadTheFollowingTask(TableNode $table)
    {
        $this->iLoadASpecificUser();
        foreach ($table->getHash() as $hash) {
            $task = TaskFactory::create(
                $hash['title'],
                $hash['content'],
                $this->user2

            );
            $this->entityManager->persist($task);
        }
        $this->entityManager->flush();
    }

    /**
     * @Given /^user with username "([^"]*)" should exist in database and have the following role "([^"]*)"$/
     */
    public function userWithUsernameShouldExistInDatabaseAndHaveTheFollowingRole($username, $role)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);
        if (\is_null($user)) {
            throw new NotFoundHttpException(
                sprintf('Not found user with username : %s', $username)
            );
        }
        if (!\in_array($role, $user->getRoles())) {
            throw new \Exception(
                sprintf(
                    'User with username %s should have the following role : %s',
                    $username,
                    $role
                )
            );
        }
    }

    /**
     * @Given I load fixtures with the following command :command
     *
     * @param $command
     *
     * @throws Exception
     */
    public function iLoadFixturesWithTheFollowingCommand($command)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput([
            'command' => $command,
            '--no-interaction' => true,
        ]);
        $output = new NullOutput();
        $application->run($input, $output);
    }

    /**
     * @Given user with username :username should have following id :userId
     *
     * @param $username
     * @param $userId
     * @throws ReflectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function userWithUsernameShouldHaveFollowingId($username, $userId)
    {
        $user = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->where('u.username = :user_username')
            ->setParameter('user_username', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (\is_null($user)) {
            throw new NotFoundHttpException(
                sprintf('Not found user with username %s', $username)
            );
        }
        $this->resetId($user, $userId);
    }

    /**
     * @Given I load a specific user
     */
    public function iLoadASpecificUser()
    {
        $this->user2 = new User(
            'user2',
            '12345678',
            'user2@gmail.com',
            'ROLE_USER'
        );
        $this->user2->setPassword(
            $this->encoderFactory->getEncoder( $this->user2)->encodePassword(
                '12345678',
                ''
            )
        );
        $this->user7 = new User(
            'user7',
            '12345678',
            'user7@gmail.com',
            'ROLE_ADMIN'
        );
        $this->user7->setPassword(
            $this->encoderFactory->getEncoder( $this->user7)->encodePassword(
                '12345678',
                ''
            )
        );
        $this->user10 = new User(
            'user10',
            '12345678',
            'user10@gmail.com',
            'ROLE_USER'
        );
        $this->user10->setPassword(
            $this->encoderFactory->getEncoder( $this->user10)->encodePassword(
                '12345678',
                ''
            )
        );
        $this->entityManager->persist($this->user2);
        $this->entityManager->persist($this->user7);
        $this->entityManager->flush();
    }

    /**
     * @Given task with title :arg1 should have following id :arg2
     * @param $title
     * @param $taskId
     * @throws ReflectionException
     * @throws NonUniqueResultException
     */
    public function taskWithTitleShouldHaveFollowingId($title, $taskId)
    {
        $task = $this->entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->where('t.title = :title')
            ->setParameter('title', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (\is_null($task)) {
            throw new NotFoundHttpException(
                sprintf('Not found task with title %s', $title)
            );
        }
        $this->resetId($task, $taskId);

    }

    /**
     * @Then task with title :title should not exist in database
     * @param $title
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function taskWithTitleShouldNotExistInDatabase($title)
    {
        $task = $this->entityManager->getRepository(Task::class)
            ->createQueryBuilder('t')
            ->where('t.title = :task_title')
            ->setParameter('task_title', $title)
            ->getQuery()
            ->getOneOrNullResult()
        ;
        if (!\is_null($task)) {
            throw new \Exception(
                sprintf('Task with title: %s should no exist but it found', $title)
            );
        }
    }


    /**
     * @Given /^I have saved one task$/
     */
    public function iHaveSavedOneTask()
    {
        $this->iLoadASpecificUser();
        $task1 = new Task(
            'tache 2',
            'Contenu de la première tâche',
            $this->user2
        );
        $this->entityManager->persist($task1);
        $this->entityManager->flush();
    }

    /**
     * @Given /^I load an other task$/
     */
    public function iLoadAnOtherTask()
    {
        $this->iLoadASpecificUser();
        $task1 = new Task(
            'tache 3',
            'Contenu de la troisieme tâche',
            $this->user10
        );
        $this->entityManager->persist($task1);
        $this->entityManager->flush();
    }


    /**
     * @param AbstractEntity $entity
     * @param string $identifier
     *
     * @throws ReflectionException
     */
    protected function resetId(AbstractEntity $entity, string $identifier)
    {
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($entity, $identifier);
        $this->entityManager->flush();
    }

    /**
     * @Given /^I save an other task$/
     */
    public function iSaveAnOtherTask()
    {
        $task3 = new Task(
            'tache 3',
            'Contenu de la troisieme tâche',
            $this->user10
        );
        $this->entityManager->persist($task3);
        $this->entityManager->flush();
    }
}
