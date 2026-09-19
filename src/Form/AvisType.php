<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvisType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('note', ChoiceType::class, [
                'label' => 'Note',
                'choices' => [
                    '1 / 5' => 1,
                    '2 / 5' => 2,
                    '3 / 5' => 3,
                    '4 / 5' => 4,
                    '5 / 5' => 5,
                ],
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Votre commentaire',
                'attr' => [
                    'rows' => 5,
                    'maxlength' => 2000,
                ],
            ]);
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}