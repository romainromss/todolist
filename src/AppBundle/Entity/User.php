<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table("todolist_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User extends AbstractEntity implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=25, unique=true)
     */
    private $username;
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64)
     */
    private $password;
    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    private $roles = [];
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=60, unique=true)
     */
    private $email;

    /**
     * User constructor.
     *
     * @param string $username
     * @param string $password
     * @param string $roles
     * @param string $email
     *
     * @throws \Exception
     */
    public function __construct(
        string $username,
        string $password,
        string $email,
        string $roles
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->roles[] = $roles;
        $this->email = $email;
        parent::__construct();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    /**
     * @return string
     */
    public function getSalt(): string
    {
        return '';
    }
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }
    /**
     * @param $password
     *
     * @return void
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    /**
     * @param $username
     *
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    /**
     * @param $email
     *
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    /**
     * @param array $roles
     *
     * @return void
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
    /**
     * @return void
     */
    public function eraseCredentials(): void
    {
    }
}
