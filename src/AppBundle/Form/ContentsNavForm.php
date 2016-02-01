<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\ContentsNav;

class ContentsNavForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idElement', HiddenType::class)
            ->add('name', HiddenType::class)
            ->add('slug', HiddenType::class)
            ->add('type', HiddenType::class)
            ->add('sort',
                IntegerType::class, [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'min' => 0,
                        'max' => count($options['contentsNav']),
                    ],
                ]
            )
            ->add('parent',
                ChoiceType::class, [
                    'choices' => $options['contentsNav'],
                    'placeholder' => 'Subcategory',
                    'label' => false,
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => ContentsNav::class,
                'contentsNav' => null,
            )
        );
    }
}
