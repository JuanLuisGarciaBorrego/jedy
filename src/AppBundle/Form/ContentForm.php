<?php

namespace AppBundle\Form;

use AppBundle\Form\FormEvent\ParentContentTranslationSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Content;

/**
 * Class ContentForm
 * @package AppBundle\Form
 */
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
     * @var Content
     */
    private $parent;

    /**
     * @param EntityManager $em
     * @param $localeActive
     */
    public function __construct(EntityManager $em, $localeActive)
    {
        $this->em = $em;
        $this->localeActive = $localeActive;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->type = $options['type'];
        $this->parent = $options['parent'];

        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'content.title',
                ]
            )
            ->add(
                'summary',
                TextareaType::class,
                [
                    'label' => 'content.summary',
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'content.content',
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
                'status',
                CheckboxType::class,
                [
                    'label' => 'content.published',
                    'required' => false,
                ]
            );

        if ($this->type == 'post') {
            if (!$this->parent) {
                $builder->add(
                    'category',
                    EntityType::class,
                    [
                        'class' => 'AppBundle\Entity\Category',
                        'query_builder' => $this->selectCategoryLocaleActive(),
                        'label' => 'content.category',
                        'placeholder' => 'content.category.select',
                        'required' => false,
                    ]
                );
            }
        }

        if ($this->parent) {
            $builder->addEventSubscriber(new ParentContentTranslationSubscriber($this->em, $this->parent));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Content::class,
                'type' => null,
                'parent' => null,
            )
        );
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function selectCategoryLocaleActive()
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryLocaleActive($this->localeActive);
    }
}
