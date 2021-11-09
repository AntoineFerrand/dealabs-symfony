<?php

namespace App\Form;

use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AccountImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'allow_file_upload' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'label' => false
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-success save'],
                'label' => 'Mettre à jour l\'avatar'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Avatar::class,
        ]);
    }
}
