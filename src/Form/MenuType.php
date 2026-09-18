<?php

namespace App\Form;

use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('nombreMinimumPersonnes')
            ->add('prixMinimum')
            ->add('conditions')
            ->add('stock')
            ->add('theme', EntityType::class, [
                'class' => Theme::class,
                'choice_label' => 'nom',
            ])
            ->add('regime', EntityType::class, [
                'class' => Regime::class,
                'choice_label' => 'nom',
            ])
            ->add('plats', EntityType::class, [
                'class' => Plat::class,
                'choice_label' => 'nom',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}