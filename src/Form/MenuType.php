<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    /**
     * Construit le formulaire de création et de modification d'un menu.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])

            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                ],
            ])

            ->add('nombreMinimumPersonnes', IntegerType::class, [
                'label' => 'Nombre minimum de personnes',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 1,
                ],
            ])

            ->add('prixMinimum', MoneyType::class, [
                'label' => 'Prix pour le nombre minimum de personnes',
                'currency' => 'EUR',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                    'step' => '0.01',
                ],
            ])

            ->add('conditions', TextareaType::class, [
                'label' => 'Conditions du menu',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 4,
                ],
            ])

            ->add('stock', IntegerType::class, [
                'label' => 'Nombre de commandes disponibles',
                'attr' => [
                    'class' => 'form-control',
                    'min' => 0,
                ],
            ])

            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'nom',
                'label' => 'Thème',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('regime', EntityType::class, [
                'class' => Regime::class,
                'choice_label' => 'nom',
                'label' => 'Régime',
                'attr' => [
                    'class' => 'form-select',
                ],
            ])

            ->add('plats', EntityType::class, [
                'class' => Plat::class,
                'choice_label' => 'nom',
                'label' => 'Plats du menu',

                /*
                 * Un menu contient plusieurs plats.
                 * Les cases à cocher permettent de choisir facilement
                 * une entrée, un plat et un dessert.
                 */
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    /**
     * Associe ce formulaire à l'entité Menu.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}