<?php

namespace App\Form;

use App\Entity\Ads;
use App\Entity\AdType;
use App\Entity\Brand;
use App\Entity\MaterialType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('city', TextType::class, [
                'label' => 'city'
            ])
            ->add('text', TextType::class, [
                'label' => 'description'
            ])
            ->add('materialType', EntityType::class, [
                'class' => MaterialType::class,
                'choice_label' => 'name'
            ])
            ->add('adType', EntityType::class, [
                'class' => AdType::class,
                'choice_label' => 'name'
            ])
            ->add('media', FileType::class, [
                'label' => 'image',
                'mapped' => false,
                'required' => false
            ])
            ->add('brand', EntityType::class, [
                'class' => Brand::class,
                'choice_label' => 'name'
            ])

            ->add('envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ads::class,
        ]);
    }
}
