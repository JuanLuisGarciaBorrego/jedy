<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UserForm
 * @package AppBundle\Form
 */
class UserForm extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username',
                TextType::class,
                [
                    'attr' => array('placeholder' => 'user.username')
                ]
            )
            ->add('plainPassword',
                PasswordType::class,
                [
                    'attr' => array('placeholder' => 'user.password'),
                    'required' => is_null($builder->getData()->getId())
                ]
            )
            ->add('email',
                EmailType::class,
                [
                    'attr' => array('placeholder' => 'user.email')
                ]
            )
            ->add('isActive',
                CheckboxType::class,
                [
                    'label' => 'user.is_active',
                    'required' => false,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
}
