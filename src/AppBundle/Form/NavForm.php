<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Nav;

class NavForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'nav.id',
                ]
            );
        $builder->add('contentsNav', CollectionType::class, array(
            'label' => false,
            'entry_type' => ContentsNavForm::class,
            'entry_options' => ['contentsNav' => $options['contentsNav']],
            'allow_add' => true,
            'allow_delete' => true,
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Nav::class,
                'contentsNav' => null,
            )
        );
    }
}
