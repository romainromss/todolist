<?php


namespace AppBundle\DataFixtures;


use AppBundle\Entity\User;
use AppBundle\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserFixtures extends Fixture
{
    /** @var EncoderFactoryInterface */
    private $encoderFactory;
    /**
     * UserFixtures constructor.
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        EncoderFactoryInterface $encoderFactory
    ) {
        $this->encoderFactory = $encoderFactory;
    }
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $dataUsers = [
            [
                'username' => 'user2',
                'password' => 'user2',
                'email' => 'user2@gmail.com',
                'roles' => false,
            ],
            [
                'username' => 'user4',
                'password' => 'user4',
                'email' => 'user4@gmail.com',
                'roles' => false,
            ],
            [
                'username' => 'user5',
                'password' => 'user5',
                'email' => 'user5@gmail.com',
                'roles' => false,
            ]
        ];
        foreach ($dataUsers as $dataUser) {
            $user = UserFactory::create(
                $dataUser['username'],
                $this->encoderFactory->getEncoder(User::class)
                    ->encodePassword($dataUser['password'], ''),
                $dataUser['email'],
                $dataUser['roles']
            );
            $manager->persist($user);
            $this->addReference($dataUser['username'], $user);
        }
        $manager->flush();
    }
}
