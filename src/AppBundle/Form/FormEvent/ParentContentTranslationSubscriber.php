<?php

namespace AppBundle\Form\FormEvent;

use AppBundle\Entity\Content;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ParentContentTranslationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Content
     */
    private $parentContent;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em, Content $parentMultilangue)
    {
        $this->em = $em;
        $this->parentContent = $parentMultilangue;
    }

    public function onPostSubmit(FormEvent $event)
    {
        $content = $event->getData();

        if (!$content->getParentMultilangue()) {
            $content->setParentMultilangue($this->parentContent);
            $content->setCategory(
                $this->selectCategoryParent($this->parentContent->getCategory(), $content->getLocale())
            );
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    private function selectCategoryParent($parentMultilangue, $locale)
    {
        return $this->em->getRepository('AppBundle:Category')
            ->selectCategoryParent($parentMultilangue, $locale);
    }
}
