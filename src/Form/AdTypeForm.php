<?php

namespace App\Form;

use App\Entity\Ads;
use App\Entity\AdType;
use App\Entity\Brand;
use App\Entity\MaterialType;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
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
                'label' => 'Ville'
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Description'
               // 'attr' => [('cols' => '5', 'rows' => '5')]
            ])
            ->add('price', TextType::class, [
                'label' => 'Prix'
            ])
            ->add('materialType', EntityType::class, [
                'label' => 'Type de matériel',
                'class' => MaterialType::class,
                'choice_label' => 'name'
            ])
            ->add('adType', EntityType::class, [
                'label' => "Type d'annonce",
                'class' => AdType::class,
                'choice_label' => 'name'
            ])
            ->add('media', FileType::class, [
                'label' => 'illustration',
                'mapped' => false,
                'required' => false
            ])
            ->add('brand', EntityType::class, [
                'label' => 'Marque',
                'class' => Brand::class,
                'choice_label' => 'name'
            ])

            ->add('envoyer', SubmitType::class, [
                'label' => 'Valider'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ads::class,
        ]);
    }
}
