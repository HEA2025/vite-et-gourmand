<?php

namespace App\Form;

use App\Entity\Horaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // L'employé modifie uniquement les heures du jour sélectionné.
        $builder
            ->add('heureOuverture', TimeType::class, [
                'label' => 'Heure d\'ouverture',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('heureFermeture', TimeType::class, [
                'label' => 'Heure de fermeture',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horaire::class,
        ]);
    }
}