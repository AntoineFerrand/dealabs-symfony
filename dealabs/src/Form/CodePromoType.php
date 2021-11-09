<?php

namespace App\Form;

use App\Entity\Deals;
use App\Entity\Groupe;
use App\Entity\Partenaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CodePromoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('website', TextType::class, [
                'help' => 'Site web du vendeur, redirigeant eventuellement sur une promotion spécifique',
                'label' => 'Lien du code promo',
                'attr' => ['placeholder' => 'https://super-codepromo.com/']
            ])
            ->add('promoCode', TextType::class, [
                'help' => 'Entrez le code promo affiché sur le site vendeur',
                'label' => 'Code promo',
                'attr' => ['placeholder' => 'CODEPROMO21']
            ])
            ->add('expirationDate', DateType::class, [
                'widget' => 'single_text',
                'help' => 'Date d\'expiration du code promo',
                'label' => 'Date d\'expiration'
            ])
            ->add('title', TextType::class, [
                'help' => 'Titre du code promo',
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Code promo de 10% à profiter rapidement']
            ])
            ->add('description', TextType::class, [
                'help' => 'Description du code promo',
                'label' => 'Description',
                'attr' => ['placeholder' => 'Vous pouvez trouver ce dernier sur le site marchand de Google']
            ])
            ->add('partenaire', EntityType::class, [
                'class' => Partenaire::class,
                'choice_label' => 'nom',
                'help' => 'Partenaire du code promo',
                'label' => 'Site web partenaire'
            ])
            ->add('groupes', EntityType::class, [
                'class' => Groupe::class,
                'choice_label' => 'nom',
                'help' => 'Sélectionnez au moins un groupe ci-dessus',
                'label' => 'Groupe',
                'multiple' => true,
                'expanded' => true,
                'required' => true
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'help' => 'L\'image du code promo à afficher',
                'label' => 'Image du code promo'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success save'],
                'label' => 'Valider'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deals::class,
        ]);
    }
}
