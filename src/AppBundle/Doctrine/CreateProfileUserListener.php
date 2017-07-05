<?php

namespace AppBundle\Doctrine;

use AppBundle\Entity\Profile;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;

class CreateProfileUserListener implements EventSubscriber
{
    protected $users;
    
    public function onFlush(OnFlushEventArgs $event)
    {
        $this->users = [];
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof User) {
                $this->users[] = $entity;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $event)
    {
        if (!empty($this->users)) {
            $em = $event->getEntityManager();

            foreach ($this->users as $user) {
                $profile = new Profile();
                $profile->setUser($user);
                $em->persist($profile);
            }

            $em->flush();
        }
    }

    public function getSubscribedEvents()
    {
        return ['onFlush', 'postFlush'];
    }
}