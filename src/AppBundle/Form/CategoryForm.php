<?php

namespace AppBundle\Form;

use AppBundle\Form\FormEvent\ParentCategoryTranslationSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Category;

/**
 * Class CategoryForm
 * @package AppBundle\Form
 */
class CategoryForm extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var string
     */
    private $localeActive;

    /**
     * @var Category
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
        $this->parent = $options['parent'];

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => 'category.name',
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => 'category.description',
                ]
            )
        ;

        if (!$this->parent) {
            $builder
                ->add(
                    'parent',
                    EntityType::class,
                    [
                        'class' => 'AppBundle\Entity\Category',
                        'query_builder' => $this->selectCategoryLocaleActive(),
                        'label' => 'category.parent',
                        'placeholder' => 'category.parent.select',
                        'required' => false,
                    ]
                );
        } else {
            $builder->addEventSubscriber(new ParentCategoryTranslationSubscriber($this->em, $this->parent));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => Category::class,
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
