<?php

namespace AppBundle\Controller;

use AppBundle\Domain\DTO\TaskDTO;
use AppBundle\Entity\Task;
use AppBundle\Factory\TaskFactory;
use AppBundle\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Twig\Environment;

class TaskController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * TaskController constructor.
     * @param Environment $twig
     * @param FormFactoryInterface $formFactory
     * @param EntityManagerInterface $entityManager
     * @param TokenStorageInterface $tokenStorage
     * @param UrlGeneratorInterface $urlGenerator
     * @param FlashBagInterface $flashBag
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        Environment $twig,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(): Response
    {
        return new Response($this->twig->render('task/list.html.twig', [
            'tasks' => $this->entityManager->getRepository(Task::class)->getAllTasks()
        ]));
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $form = $this->formFactory->create(TaskType::class)
            ->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $task = TaskFactory::create(
                $form->getData()->title,
                $form->getData()->content,
                $user
            );
            $this->entityManager->persist($task);
            $this->entityManager->flush();
            $this->flashBag->add('success', 'La tâche a été bien été ajoutée.');

            return new RedirectResponse($this->urlGenerator->generate('task_list'));
        }

        return new Response(
            $this->twig->render('task/create.html.twig', [
                'form' => $form->createView()
            ])
        );
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     *
     * @param string $id
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function editAction(
        string $id,
        Request $request
    ):  Response {
        $task = $this->entityManager->getRepository(Task::class)->getTaskById($id);

        if (is_null($task)) {
            $this->flashBag->add(
                'error',
                'La tâche demandée n\'existe pas.'
            );
            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        $form = $this->formFactory->create(
            TaskType::class, new TaskDTO(
                $task->getTitle(),
                $task->getContent()
            )
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setTitle($form->getData()->title);
            $task->setContent($form->getData()->content);

            $this->entityManager->flush();
            $this->flashBag->add(
                'success',
                'La tâche a bien été modifiée.'
            );
            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        return new Response($this->twig->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task
        ])
        );
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     *
     * @param string $id
     *
     * @return Response
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function toggleTaskAction(string $id): Response
    {
        $task = $this->entityManager->getRepository(Task::class)->getTaskById($id);
        if (is_null($task)) {
            $this->flashBag->add(
                'error',
                'La tâche demandée n\'existe pas.'
            );
            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }

        $task->toggle();
        $this->entityManager->flush();

        $message = 'non faite';
        if ($task->isDone()) {
            $message = 'faite';
        }
        $this->flashBag->add('success', 'La tâche ' .$task->getTitle().' a bien été marquée comme '. $message);
        return new RedirectResponse(
            $this->urlGenerator->generate('task_list')
        );

    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     *
     * @param string $id
     *
     * @return RedirectResponse
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function deleteTaskAction(string $id)
    {
        $task = $this->entityManager->getRepository(Task::class)->getTaskById($id);

        if (is_null($task)) {
            $this->flashBag->add('error', 'La tâche demandée n\'existe pas.');
            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }
        $user = $this->tokenStorage->getToken()->getUser();

        if ($task->getUser() === $user || ($task->getUser() === null &&
                $this->authorizationChecker->isGranted('ROLE_ADMIN'))
        ) {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
            $this->flashBag->add(
                'success',
                'La tâche a bien été supprimée.'
            );

            return new RedirectResponse(
                $this->urlGenerator->generate('task_list')
            );
        }
        $this->flashBag->add(
            'error',
            'Vous ne pouvez pas supprimer cette tâche.
            ');

        return new RedirectResponse(
            $this->urlGenerator->generate('task_list')
        );
    }
}
