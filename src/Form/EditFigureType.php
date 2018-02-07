<?php

namespace App\Form;

use App\Entity\Figure;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EditFigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom de la figure'])
            ->add('description', TextareaType::class)
            ->add('group', EntityType::class, [
                'class' => Group::class,
                'choice_label' => 'name',
            ])
            ->add('images', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => [
                    "label" => false,
                    "attr"=>["class" => "my-input-add-cont"]
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'mapped' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'entry_options' => [
                    "label" => false,
                    "attr"=>["class" => "my-input-add-cont"]
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // uncomment if you want to bind to a class
            'data_class' => Figure::class,
        ]);
    }
}
