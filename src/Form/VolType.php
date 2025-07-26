<?php
// src/Form/VolType.php

namespace App\Form;

use App\Entity\Vol;
use App\Entity\Ville;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero', TextType::class, [
                'label' => 'Numéro du vol',
                'disabled' => true // Le numéro du vol est généré automatiquement
            ])
            ->add('heure_depart', DateTimeType::class, [
                'label' => 'Heure de départ'
            ])
            ->add('heure_arrivee', DateTimeType::class, [
                'label' => 'Heure d\'arrivée'
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix'
            ])
            ->add('reduction', CheckboxType::class, [
                'label' => 'Réduction',
                'required' => false
            ])
            ->add('places_disponibles', NumberType::class, [
                'label' => 'Places disponibles'
            ])
            ->add('villeDepart', null, [
                'label' => 'Ville de départ',
                'choice_label' => 'nom',
                'class' => Ville::class
            ])
            ->add('villeArrivee', null, [
                'label' => 'Ville d\'arrivée',
                'choice_label' => 'nom',
                'class' => Ville::class
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vol::class,
        ]);
    }
}