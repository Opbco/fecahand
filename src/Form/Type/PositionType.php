<?php

namespace App\Form\Type;

use App\Entity\Position;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PositionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nom', TextType::class, [
                    'label' => 'Nom',
                ])
                ->add('responsable', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false
                    ],
                    'label' => 'Est-il responsable',
                ])
                ->add('licenced', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false
                    ],
                    'label' => 'Sujet a une licence',
                ])
                ->add('insured', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false
                    ],
                    'label' => 'Sujet a une assurance',
                ])
                ->add('apte', ChoiceType::class, [
                    'choices' => [
                        'Oui' => true,
                        'Non' => false
                    ],
                    'label' => "Sujet a un certificat d'aptitude",
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Position::class,
        ]);
    }
}