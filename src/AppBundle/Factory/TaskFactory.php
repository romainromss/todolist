<?php


namespace AppBundle\Factory;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;

class TaskFactory
{

    /**
     * @param string $title
     * @param string $content
     * @param User|null $user
     * @return Task
     * @throws \Exception
     */
    public static function create(
        string $title,
        string $content,
        $user
    ) {
        $task = new Task(
            $title,
            $content,
            $user
        );
        return $task;
    }
}
