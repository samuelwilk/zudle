<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    private const TEXT_INPUT_CLASS = 'bg-zu-white border border-zu-gray text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-zu-lighter-black dark:border-zu-gray dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500';
    private const LABEL_CLASS = 'block mb-2 text-sm font-medium text-gray-900 dark:text-white';
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'label_attr' => ['class' => self::LABEL_CLASS],
                'attr' => [
                    'class' => self::TEXT_INPUT_CLASS,
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'First Name',
                'label_attr' => ['class' => self::LABEL_CLASS],
                'attr' => [
                    'class' => self::TEXT_INPUT_CLASS,
                ],
            ])
            ->add('username', TextType::class, [
                'label' => 'Username',
                'label_attr' => ['class' => self::LABEL_CLASS],
                'attr' => [
                    'class' => self::TEXT_INPUT_CLASS,
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'First Name',
                'label_attr' => ['class' => self::LABEL_CLASS],
                'attr' => [
                    'class' => self::TEXT_INPUT_CLASS,
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the handler
                'type' => PasswordType::class,
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'first_options' => [
                    'label' => 'Password',
                    'label_attr' => ['class' => self::LABEL_CLASS],
                    'attr' => ['autocomplete' => 'new-password', 'class' => self::TEXT_INPUT_CLASS],
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'label_attr' => ['class' => self::LABEL_CLASS],
                    'attr' => ['autocomplete' => 'new-password', 'class' => self::TEXT_INPUT_CLASS],
                ],
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
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'class' => 'mt-4 w-full text-white bg-zu-red hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-zu-gray dark:hover:bg-primary-700 dark:focus:ring-primary-800',
                ],
                'label' => 'Register',
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
