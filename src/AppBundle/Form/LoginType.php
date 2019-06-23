<?php


namespace AppBundle\Form;

use AppBundle\Domain\DTO\LoginDTO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LoginType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add(
                'username',
                TextType::class,
                [
                    'required' => false,
                    'label'    => 'Nom d\'utilisateur',
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'required' => false,
                    'label'    => 'Mot de passe',
                ]
            );
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => LoginDTO::class,
                'empty_data' => function (FormInterface $form) {
                    return new LoginDTO(
                        $form->get('username')->getData(),
                        $form->get('password')->getData()
                    );
                },
            ]
        );
    }
}
