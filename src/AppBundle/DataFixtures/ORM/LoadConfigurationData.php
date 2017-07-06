<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Configuration;

class LoadConfigurationData implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $config = new Configuration();
        $config->setEnableBlog(true);

        $manager->persist($config);
        $manager->flush();
    }
}