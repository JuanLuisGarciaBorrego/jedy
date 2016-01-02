<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name'
            ])
            ->add('parent', EntityType::class, [
                'class' => 'AppBundle\Entity\Category',
                'label' => 'subcategory',
                'placeholder' => 'select parent',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {

    }
}
