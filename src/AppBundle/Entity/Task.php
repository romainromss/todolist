<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("todolist_task")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TaskRepository")
 */
class Task extends AbstractEntity
{
    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="Vous devez saisir un titre.")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous devez saisir du contenu.")
     */
    private $content;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDone;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", cascade={"persist"})
     */
    private $user;

    /**
     * Task constructor.
     *
     * @param string $title
     * @param string $content
     * @param User|null $user
     *
     * @throws \Exception
     */
    public function __construct(
        string $title,
        string $content,
        ?User $user = null
    ) {
        $this->isDone = false;
        $this->title = $title;
        $this->content = $content;
        $this->user = $user;
        parent::__construct();
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isDone()
    {
        return $this->isDone;
    }

    public function toggle()
    {
        $this->isDone = !$this->isDone;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
