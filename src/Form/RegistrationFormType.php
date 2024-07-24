<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName')
            ->add('lastName')
            ->add('email')
            ->add('username', TextType::class, ['required' => false])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the handler
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => ['label' => 'Password', 'attr' => ['autocomplete' => 'new-password']],
                'second_options' => ['label' => 'Repeat Password', 'attr' => ['autocomplete' => 'new-password']],
                'constraints' => [
                    new NotBlank(message: 'Please enter a password'),
                    new Length(
                        min: 6,
                        max: 4096,
                        // max length allowed by Symfony for security reasons
                        minMessage: 'Your password should be at least {{ limit }} characters',
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
