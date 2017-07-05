<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'first_name', 
            TextType::class, 
            [
                'attr' => array('placeholder' => 'profile.first_name'),
                'required' => false
            ]
        )
        ->add(
            'last_name', 
            TextType::class, 
            [
                'attr' => array('placeholder' => 'profile.last_name'),
                'required' => false
            ]
        )
        ->add(
            'email', 
            EmailType::class, 
            [
                'attr' => array('placeholder' => 'profile.public_email'),
                'required' => false
            ]
        )
        ->add(
            'location', 
            TextType::class,
            [
                'attr' => array('placeholder' => 'profile.location'),
                'required' => false
            ]
        )
        ->add(
            'about',
            TextareaType::class,
            [
                'label' => 'profile.about',
                'required' => false,
            ]
        )
        ->add(
            'photo',
            FileType::class,
            [
                'label' => 'profile.photo',
                'required' => false,
                'data_class' => null
            ]
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Profile'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_profile';
    }


}
