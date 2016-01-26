<?php

namespace AppBundle\Form\NavContents;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NavPageForm extends AbstractType
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
                'page',
                EntityType::class,
                [
                    'class' => 'AppBundle\Entity\Content',
                    'query_builder' => $this->selectPagesLocaleActive(),
                    'label' => 'nav.page.add',
                    'placeholder' => 'nav.page.select',
                    'required' => true,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'em' => null,
                'locale_active' => null,
            )
        );
    }

    public function selectPagesLocaleActive()
    {
        return $this->em->getRepository('AppBundle:Content')
            ->selectPagesLocaleActive($this->localeActive);
    }
}
