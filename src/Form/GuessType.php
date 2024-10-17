<?php

namespace App\Form;

use App\Entity\Guess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('guess', CollectionType::class, [
                'entry_type' => TextType::class,
                'label' => false,
                'prototype' => true,
                'allow_add' => false,
                'allow_delete' => false,
                'entry_options' => [
                    'attr' => [
                        'class' => 'guess-input', 'data-keyboard-target' => 'guessInput',
                        'disabled' => true,
                        'maxlength' => 1,
                    ],
                ],
                'getter' => function (Guess $guess) use ($options) {
                    // Initialize with empty strings or split existing guess
                    $length = $options['length'];
                    $guessString = $guess->getGuess();

                    if (null === $guessString) {
                        return array_fill(0, $length, '');
                    }

                    $guessArray = str_split($guessString);

                    return array_pad($guessArray, $length, '');
                },
                'setter' => function (Guess $guess, array $submittedData, FormInterface $form) {
                    // setter is implemented in the GuessForm instantiateForm
                },
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Guess::class,
            'length' => 1,
        ]);
        $resolver->setAllowedTypes('length', 'int');
        $resolver->setRequired(['length']);
    }
}
