<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentsNavForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'idElement',
                IntegerType::class
            )
            ->add(
                'name',
                TextType::class
            )
            ->add('type',
                ChoiceType::class,[
                    'choices' => [
                        'page' => 'Page',
                        'post' => 'Post'
                    ]
                ]
            )
            ->add('sort',
                IntegerType::class
            )
            ->add('parent',
                IntegerType::class
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\ContentsNav',
            )
        );
    }

}
