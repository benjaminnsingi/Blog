<?php

namespace App\Form;

use App\Entity\Categories;
use App\Entity\Post;
use App\Form\Type\DateTimePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the custom form field type used to manipulate datetime values across
 */
class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                  'label' => 'label.title',
                  'attr' => [
                      'class' => 'form-control'
                  ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'label.content',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('publishedAt', DateTimePickerType::class, [
                 'label' => 'label.published_at'
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
