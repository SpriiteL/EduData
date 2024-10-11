<?php
// src/Form/EtablishmentType.php

namespace App\Form;

use App\Entity\Etablishment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EtablishmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de l\'établissement',
                'required' => true,
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse de l\'établissement',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Etablishment::class,
        ]);
    }
}