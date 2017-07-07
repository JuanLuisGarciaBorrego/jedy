<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ConfigurationForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titleSite', 
            TextType::class,
            [
                'attr' => array('placeholder' => 'configuration.site_title'),
                'required' => false
            ]
        )
        ->add('descriptionSite',
            TextType::class,
            [
                'attr' => array('placeholder' => 'configuration.site_description'),
                'required' => false
            ]
        )
        ->add('enableBlog',
            CheckboxType::class,
            [
                'attr' => array('placeholder' => 'configuration.enable_blog'),
                'required' => false
            ]
        )
        ->add('content', EntityType::class, array(
            'required' => false,
            'placeholder' => 'configuration.index_page',
            'class' => 'AppBundle:Content',
            'query_builder' => function (\AppBundle\Repository\ContentRepository $repository)
            {
                return $repository->getPages();
            }
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Configuration'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_configuration';
    }


}
