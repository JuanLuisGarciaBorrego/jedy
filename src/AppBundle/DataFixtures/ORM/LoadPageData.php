<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Content;

class LoadPageData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
        $locales = $this->container->get('locales');

        $page = new Content($locales->getLocaleActive());
        $page->setTitle('What is a Jedi?');
        $page->setSummary('A Jedi was a Force-sensitive individual, most often a member of the Jedi Order, who studied, served, and used the mystical energies of the Force; usually, the light side of the Force');
        $page->setContent('The weapon of a Jedi was the lightsaber, a blade made of pure energy. Jedi fought for peace and justice in the Galactic Republic, usually against their mortal enemy: the Sith, who studied the dark side of the Force. The Jedi were all but destroyed by the Sith during and after the execution of Order 66, leaving very few Jedi survivors until there was only one known living Jedi, Luke Skywalker, at the end of the Galactic Civil War.');
        $page->setType('page');
        $page->setStatus(true);

        $manager->persist($page);

        foreach ($locales->getLocales() as $locale) {
            if (!$locale['active']) {
                $translationPage = new Content($locale['code']);
                $translationPage->setTitle($locale['code'].' What is a Jedy?');
                $translationPage->setSummary($locale['code'].' A Jedi was a Force-sensitive individual, most often a member of the Jedi Order, who studied, served, and used the mystical energies of the Force; usually, the light side of the Force');
                $translationPage->setContent($locale['code'].' The weapon of a Jedi was the lightsaber, a blade made of pure energy. Jedi fought for peace and justice in the Galactic Republic, usually against their mortal enemy: the Sith, who studied the dark side of the Force. The Jedi were all but destroyed by the Sith during and after the execution of Order 66, leaving very few Jedi survivors until there was only one known living Jedi, Luke Skywalker, at the end of the Galactic Civil War.');
                $translationPage->setType('page');
                $translationPage->setStatus(true);
                $translationPage->setParentMultilangue($page);

                $manager->persist($translationPage);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
