<?php


namespace AppBundle\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TaskDTO
{
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le titre de la tâche doit être renseigné.")
     * @Assert\Length(
     *     min="3",
     *     minMessage="Le titre de la tâche doit comporter au moins {{ limit }} caractères.",
     *     max="25",
     *     maxMessage="Le titre de la tâche ne doit pas comporter plus de {{ limit }} caractères."
     * )
     */
    public $title;
    /**
     * @var string
     *
     * @Assert\NotNull(message="Le contenu de la tâche doit être renseigné.")
     * @Assert\Length(
     *     min="5",
     *     minMessage="Le contenu de la tâche doit comporter au moins {{ limit }} caractères."
     * )
     */
    public $content;
    /**
     * TaskDTO constructor.
     * @param string $title
     * @param string $content
     */
    public function __construct(
        ?string $title = null,
        ?string $content = null
    ) {
        $this->title = $title;
        $this->content = $content;
    }
}
