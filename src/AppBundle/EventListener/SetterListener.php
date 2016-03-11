<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\Content;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use AppBundle\Entity\Category;
use Cocur\Slugify\Slugify;

/**
 * Class SetterListener
 * Set properties of the Content and Category class
 * - slug
 * - publishAt
 *
 * @package AppBundle\EventListener
 */
class SetterListener implements EventSubscriber
{
    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * @param Slugify $slugify
     */
    public function __construct(Slugify $slugify)
    {
        $this->slugify = $slugify;
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->slug($args);
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->slug($args);
        $this->publishedAt($args);
    }

    public function publishedAt(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Content) {
            $entity->setPublishedAt(new \DateTime('now'));
        }
    }

    public function slug(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof Category) {
            $entity->setSlug($this->slugify->slugify($entity->getName()));
        }

        if ($entity instanceof Content) {
            $entity->setSlug($this->slugify->slugify($entity->getTitle()));
        }
    }

    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate',
        ];
    }
}
