<?php

namespace App\Form;

use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notation', IntegerType::class, [
                'label' => "Note",
                'attr' => [
                    'placeholder' => '0',
                    'min' => 0,
                    'max' => 5
                ]
            ])
            ->add('comment', TextareaType::class, [
                'label' => "Commentaire",
                'attr' => [
                    'placeholder' => 'Votre commentaire'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rating::class,
        ]);
    }
}
