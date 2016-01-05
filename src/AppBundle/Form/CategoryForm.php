<?php

namespace AppBundle\Form;

use AppBundle\Form\FormEvent\ParentCategoryTranslationSubscriber;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Category;

class CategoryForm extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Category
     */
    private $parent;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->parent = $options['parent'];

        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
            ]);

        if (!$this->parent) {
            $builder
                ->add('parent', EntityType::class, [
                    'class' => 'AppBundle\Entity\Category',
                    'label' => 'subcategory',
                    'placeholder' => 'select parent',
                    'required' => false
                ]);
        } else {
            $builder->addEventSubscriber(new ParentCategoryTranslationSubscriber($this->em, $this->parent));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Category',
            'parent' => null,
        ));
    }
}
