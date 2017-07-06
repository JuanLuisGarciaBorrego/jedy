<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\Profile;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $userAdminProfile = new Profile();
        $userAdminProfile->setEmail('hola@jedy.com');
        $userAdminProfile->setFirstName('Jedy!');
        $userAdminProfile->setLocation('Galaxy');

        $manager->persist($userAdminProfile);
        $manager->flush();

        $userAdmin = new User();
        $userAdmin->setUsername('jedy');

        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($userAdmin, '1234');
        $userAdmin->setPassword($password);
        $userAdmin->setEmail("admin@jedy.com");
        $userAdmin->setIsActive(true);
        $userAdmin->setProfile($userAdminProfile);

        $manager->persist($userAdmin);

        $manager->flush();
    }
}