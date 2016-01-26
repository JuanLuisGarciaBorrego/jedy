<?php

namespace AppBundle\Form\FormEvent;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ParentCategoryTranslationSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Category
     */
    private $parentCategory;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em, Category $parentCategory)
    {
        $this->em = $em;
        $this->parentCategory = $parentCategory;
    }

    public function onPostSetData(FormEvent $event)
    {
        $category = $event->getData();

        $category->setParentMultilangue($this->parentCategory);
        $category->setParent($this->selectCategoryParent($this->parentCategory->getParent(), $category->getLocale()));
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'onPostSetData',
        ];
    }

    private function selectCategoryParent($parentMultilangue, $locale)
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryParent($parentMultilangue, $locale);
    }
}
