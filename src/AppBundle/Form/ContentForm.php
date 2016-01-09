<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Title',
                ]
            )
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'page' => 'Page',
                    'post' => 'Post'
                ],
                'placeholder' => 'Select type content'
            ])
            ->add('category',
                EntityType::class,
                [
                    'class' => 'AppBundle\Entity\Category',
                    'label' => 'subcategory',
                    'placeholder' => 'select parent',
                    'required' => false,
                ])
            ->add('parentMultilangue', EntityType::class,
                [
                    'class' => 'AppBundle\Entity\Content',
                    'label' => 'Translation',
                    'placeholder' => 'select translation',
                    'required' => false,
                ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Content',
            )
        );
    }
}
