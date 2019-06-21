<?php

namespace AppBundle\Controller;

use AppBundle\Domain\DTO\UserRegisterDTO;
use AppBundle\Entity\User;
use AppBundle\Factory\UserFactory;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Twig\Environment;

class UserController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * UserController constructor.
     * @param Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param FlashBagInterface $flashBag
     * @param EntityManagerInterface $entityManager
     * @param FormFactoryInterface $formFactory
     * @param EncoderFactoryInterface $encoderFactory
     * @param UserFactory $userFactory
     */
    public function __construct(
        Environment $twig,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        EncoderFactoryInterface $encoderFactory,
        UserFactory $userFactory
    ) {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->encoderFactory = $encoderFactory;
        $this->userFactory = $userFactory;
    }

    /**
     * @Route("/users", name="user_list")
     *
     * @return Response
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function listAction()
    {
        return new Response(
            $this->twig->render(
                'user/list.html.twig',
                [
                    'users' => $this->entityManager->getRepository(User::class)->getAllUsers()
                ]
            )
        );
    }

    /**
     * @Route("/users/create", name="user_create")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createAction(Request $request): Response
    {
        $form = $this->formFactory->create(UserType::class)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $encoder = $this->encoderFactory->getEncoder(User::class);
            $user = UserFactory::create(
                $form->getData()->username,
                $encoder->encodePassword($form->getData()->password, ''),
                $form->getData()->email,
                $form->getData()->roles
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', "L'utilisateur a bien été ajouté.");

            return new RedirectResponse($this->urlGenerator->generate('user_list'));
        }

        return new Response($this->twig->render(
            'user/create.html.twig', ['form' => $form->createView()])
        );
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
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

        $user = $this->entityManager->getRepository(User::class)->getUserById($id);
        $encoder = $this->encoderFactory->getEncoder(User::class);

        if (is_null($user)) {
            $this->flashBag->add('error', "L'utilisateur demandé n'existe pas.");
            return new RedirectResponse(
                $this->urlGenerator->generate('user_list')
            );
        }
        $form = $this->formFactory->create(
            UserType::class,
            new UserRegisterDTO(
                $user->getUsername(),
                $user->getPassword(),
                $user->getRoles()[0],
                $user->getEmail()
            )
        )->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($form->getData()->username);
            $user->setPassword($encoder->encodePassword($form->getData()->password, ''));
            $user->setRoles([$form->getData()->roles]);
            $user->setEmail($form->getData()->email);

            $this->entityManager->flush();
            $this->flashBag->add(
                'success',
                "L'utilisateur a bien été modifié"
            );
            return new RedirectResponse(
                $this->urlGenerator->generate('user_list')
            );
        }

        return new Response(
            $this->twig->render(
                'user/edit.html.twig', [
                'form' => $form->createView(),
                'user' => $user
            ])
        );
    }
}
