<?php

namespace AppBundle\Form;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContentForm extends AbstractType
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $localeActive;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, $localeActive)
    {
        $this->em = $em;
        $this->localeActive = $localeActive;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->type = $options['type'];

        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Title',
                ]
            )
            ->add(
                'type',
                HiddenType::class,
                [
                    'data' => $this->type,
                ]
            )
            ->add(
                'parentMultilangue',
                EntityType::class,
                [
                    'class' => 'AppBundle\Entity\Content',
                    'label' => 'Translation',
                    'placeholder' => 'select translation',
                    'required' => false,
                ]
            );

        if ($this->type == 'post') {

            $builder->add(
                'category',
                EntityType::class,
                [
                    'class' => 'AppBundle\Entity\Category',
                    'query_builder' => $this->selectCategoryLocaleActive(),
                    'label' => 'subcategory',
                    'placeholder' => 'select parent',
                    'required' => false,
                ]
            );
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Content',
                'type' => null,
            )
        );
    }

    public function selectCategoryLocaleActive()
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryLocaleActive($this->localeActive);
    }
}
