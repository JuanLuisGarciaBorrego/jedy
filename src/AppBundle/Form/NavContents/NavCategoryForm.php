<?php

namespace AppBundle\Form\NavContents;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavCategoryForm extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $localeActive;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->em = $options['em'];
        $this->localeActive = $options['locale_active'];

        $builder
            ->add(
                'parent',
                EntityType::class,
                [
                    'class' => 'AppBundle\Entity\Category',
                    'query_builder' => $this->selectCategoryLocaleActive(),
                    'label' => 'Add category',
                    'placeholder' => 'Add category',
                    'required' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'em' => null,
                'locale_active' => null
            )
        );
    }

    public function selectCategoryLocaleActive()
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryLocaleActive($this->localeActive);
    }
}
