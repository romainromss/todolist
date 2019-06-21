<?php


namespace AppBundle\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LoginDTO
{
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le nom d'utilisateur doit être renseigné.")
     */
    public $username;
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le mot de passe doit être renseigné.")
     */
    public $password;
    /**
     * ConnectionDTO constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct(
        ?string $username = null,
        ?string $password = null
    ) {
        $this->username = $username;
        $this->password = $password;
    }
}
