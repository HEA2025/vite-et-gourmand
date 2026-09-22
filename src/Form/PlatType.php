<?php

namespace App\Form;

use App\Entity\Allergene;
use App\Entity\Plat;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlatType extends AbstractType
{
    /**
     * Construit le formulaire de création et de modification d'un plat.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
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

            ->add('type', TextType::class, [
                'label' => 'Type',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Exemple : entrée, plat ou dessert',
                ],
            ])

            ->add('allergenes', EntityType::class, [
                'class' => Allergene::class,
                'choice_label' => 'nom',
                'label' => 'Allergènes',

                /*
                 * Plusieurs allergènes peuvent être associés au même plat.
                 * Les cases à cocher évitent la sélection multiple avec Ctrl.
                 */
                'multiple' => true,
                'expanded' => true,

                /*
                 * Aucun allergène n'est obligatoire.
                 * Une sélection vide représente donc un plat sans allergène.
                 */
                'required' => false,
            ])
        ;
    }

    /**
     * Associe ce formulaire à l'entité Plat.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plat::class,
        ]);
    }
}