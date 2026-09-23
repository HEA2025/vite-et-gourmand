<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfilType extends AbstractType
{
    /**
     * Formulaire permettant à l'utilisateur de modifier ses informations.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre nom.'),
                    new Length(
                        max: 20,
                        maxMessage: 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre prénom.'),
                    new Length(
                        max: 20,
                        maxMessage: 'Le prénom ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre numéro de téléphone.'),
                    new Length(
                        max: 20,
                        maxMessage: 'Le numéro de téléphone ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('adresse', TextType::class, [
                'label' => 'Adresse postale',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre adresse.'),
                    new Length(
                        max: 255,
                        maxMessage: 'L’adresse ne peut pas dépasser {{ limit }} caractères.',
                    ),
                ],
            ])

            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir votre adresse e-mail.'),
                    new Email(message: 'Veuillez saisir une adresse e-mail valide.'),
                ],
            ])
        ;
    }

    /**
     * Associe le formulaire à l'entité Utilisateur.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}