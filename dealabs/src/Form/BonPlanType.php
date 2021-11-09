<?php

namespace App\Form;

use App\Entity\Deals;
use App\Entity\Groupe;
use App\Entity\Partenaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class BonPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('link', TextType::class, [
                'help' => 'Lien redirigeant sur le bon plan en question',
                'label' => 'Lien du bon plan',
                'attr' => ['placeholder' => 'https://e-commerce.com/super-bon-plan']
            ])
            ->add('title', TextType::class, [
                'help' => 'Titre du bon plan',
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Smartphone 128 Go + Tablette (via ODR de 100 euros)'],
            ])
            ->add('description', TextType::class, [
                'help' => 'Description du bon plan',
                'label' => 'Description',
                'attr' => ['placeholder' => 'Vu sur le site e-commerce, depuis hier matin !'],
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
            /*->addEventListener(
                FormEvents::PRE_SET_DATA,
                array($this, 'onPreSetData')
            )*/
            ->add('normalPrice', MoneyType::class, [
                'help' => 'Prix régulier de l\'article du bon plan',
                'label' => 'Prix régulier',
                'attr' => ['placeholder' => '189,99'],
            ])
            ->add('discountPrice', MoneyType::class, [
                'help' => 'Prix mis en réduction du bon plan',
                'label' => 'Prix en promotion',
                'attr' => ['placeholder' => '159,99'],
            ])
            ->add('shipping', MoneyType::class, [
                'help' => 'Prix de livraison de l\'article bon plan',
                'label' => 'Prix de livraison',
                'attr' => ['placeholder' => '6,99'],
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_label' => '...',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'help' => 'L\'image du bon plan à afficher',
                'label' => 'Image'
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success save'],
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deals::class,
            'error_mapping' => [
                '.' => 'groupes',
            ]
        ]);
    }

    /*
    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ( $data->getPartenaire() != null ) {
            $form->add('partenaire', EntityType::class, [
                'class' => Partenaire::class,
                'choice_label' => 'nom',
                'help' => 'Partenaire du code promo',
                'label' => 'Site web partenaire',
                'disabled' => true
            ]);
            $form->add('partenaireLabel', TextType::class, [
                'help' => 'Label du nouveau partenaire',
                'label' => 'Label',
                'attr' => ['placeholder' => 'Google'],
                'mapped' => false
            ]);
            $form->add('partenaireUrl', TextType::class, [
                'help' => 'URL du nouveau partenaire',
                'label' => 'URL',
                'attr' => ['placeholder' => 'https://store.google.com/'],
                'mapped' => false
            ]);
        }
    }
    */
}
