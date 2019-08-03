<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Movie;
use App\Entity\People;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du film'
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug du film'
            ])
            ->add('poster', UrlType::class, [
                'label' => "URL de l'affiche du film"
            ])
            ->add('synopsis', TextareaType::class, [
                'label' => "Synopsis du film"
            ])
            ->add('categories', EntityType::class, [
                'label' => 'Catégories',
                'class' => Category::class,
                'choice_label' => 'title',
                'multiple' => true,
                'expanded' => true
            ])
            ->add('director', EntityType::class, [
                'label' => 'Réalisateur',
                'class' => People::class,
                'choice_label' => function($people) {
                    return $people->getFullName();
                },
                'expanded' => true
            ])
            ->add('actors', EntityType::class, [
                'label' => 'Acteurs',
                'class' => People::class,
                'choice_label' => function($people) {
                    return $people->getFullName();
                },
                'multiple' => true,
                'expanded' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
