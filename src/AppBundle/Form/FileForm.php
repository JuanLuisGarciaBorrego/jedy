<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

class FileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,
                [
                    'label' => 'title',
                    'mapped' => false,
                    'required' => false
                ]
            )
            ->add('file', FileType::class,
                [
                    'label' => 'Upload file',
                    'mapped' => false
                ]
            );
    }
}
