<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('old_password', PasswordType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Ancien mot de passe']
            ])
            ->add('new_password', PasswordType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Nouveau mot de passe']
            ])
            ->add('confirm_new_password', PasswordType::class, [
                'label' => false,
                'attr' => ['placeholder' => 'Confirmer le nouveau mot de passe']
            ])
            ->add('reset', ResetType::class, [
                'attr' => ['class' => 'btn btn-success save'],
                'label' => 'Annuler'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success save'],
                'label' => 'Mettre à jour son mot de passe'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
