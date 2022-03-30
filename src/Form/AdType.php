<?php

namespace App\Form;

use App\Entity\Ads;
use App\Entity\AdTypes;
use App\Entity\MaterialType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdType extends AbstractType
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
                'class' => AdTypes::class,
                'choice_label' => 'name'
            ])
            ->add('media')
            ->add('brand')
            ->add('author')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ads::class,
        ]);
    }
}
