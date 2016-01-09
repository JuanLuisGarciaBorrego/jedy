<?php

namespace AppBundle\Form\FormEvent;

use AppBundle\Entity\Content;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ParentContentSubscriber implements EventSubscriberInterface
{
    /**
     * @var Content
     */
    private $parentContent;

    public function __construct(Content $parentMultilangue)
    {
        $this->parentContent = $parentMultilangue;
    }

    public function onPostSubmit(FormEvent $event)
    {
        $content = $event->getData();
        $content->setParentMultilangue($this->parentContent);
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }
}