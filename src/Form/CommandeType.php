<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adressePrestation', TextType::class, [
                'label' => 'Adresse de la prestation',
                'attr' => [
                    'placeholder' => 'Ex : 10 rue Sainte-Catherine, Bordeaux',
                ],
            ])
            ->add('datePrestation', DateType::class, [
                'label' => 'Date de la prestation',
                'widget' => 'single_text',
            ])
            ->add('heureLivraison', TimeType::class, [
                'label' => 'Heure souhaitée de livraison',
                'widget' => 'single_text',
            ])
            ->add('nombrePersonnes', IntegerType::class, [
                'label' => 'Nombre de personnes',
                'attr' => [
                    'min' => 1,
                ],
            ])
            ->add('distanceLivraison', NumberType::class, [
                'label' => 'Distance depuis Bordeaux (km)',
                'required' => false,
                'scale' => 1,
                'attr' => [
                    'min' => 0,
                    'step' => '0.1',
                    'placeholder' => '0 si la prestation est à Bordeaux',
                ],
                'help' => 'Laissez 0 pour Bordeaux. Hors Bordeaux, indiquez la distance en kilomètres.',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}