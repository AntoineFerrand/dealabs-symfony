<?php

namespace App\Form;

use App\Entity\Alerte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AlerteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('motscles', TextType::class, [
                'help' => 'Mots Clés correspondant au produit que vous recherchez',
                'label' => 'Mots Clés',
                'attr' => ['placeholder' => 'Chaise en bois']
            ])
            ->add('temperature', IntegerType::class, [
                'help' => 'Température à partir de laquelle vous voulez être notifié(e)',
                'label' => 'Température minimum',
                'attr' => [
                    'placeholder' => '100',
                    'min' => '0',
                    'max' => '300'
                ]
            ] )
            ->add('notification', CheckboxType::class, [
                'label' => 'Recevoir une notification pour cette alerte',
                'required' => false,
            ] )
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success save'],
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Alerte::class,
        ]);
    }
}
