<?php

namespace App\Form;

use App\Entity\Guess;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GuessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('guess', CollectionType::class, [
                'entry_type' => TextType::class,
                'entry_options' => [
                    'attr' => ['maxlength' => 1, 'class' => 'guess-input', 'data-keyboard-target' => 'guessInput'],
                ],
                'getter' => function (Guess $guess) use ($options) {
                    // Initialize with empty strings or split existing guess
                    $length = $options['length'];
                    $guessString = $guess->getGuess();

                    return $guessString ? str_split($guessString) : array_fill(0, $length, '');
                },
                'setter' => function (Guess $guess, $submittedData) {
                    // Combine the array of characters back into a string
                    $guess->setGuess(implode('', $submittedData));
                },
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
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

    public function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        $length = $form->getConfig()->getOption('length');
        $guessWord = '';
        for ($i = 0; $i < $length; ++$i) {
            if (isset($data['letter'.$i])) {
                $guessWord .= $data['letter'.$i];
                // remove the letter from the data array
                unset($data['letter'.$i]);
            }
        }
        $data['guess'] = $guessWord;
        $event->setData($data);
    }
}
