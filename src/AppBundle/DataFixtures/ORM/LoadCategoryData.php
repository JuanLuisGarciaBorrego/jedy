<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $category = new Category($locales->getLocaleActive());
        $category->setName('Light side of the Force');
        $category->setDescription(
            'The Light Side of the Force was aligned with calmness and was used for knowledge and defense'
        );

        $manager->persist($category);

        $this->addReference('category', $category);

        foreach ($locales->getLocales() as $locale) {
            if (!$locale['active']) {
                $translationCategory = new Category($locale['code']);
                $translationCategory->setName($locale['code'].' Light side of the Force');
                $translationCategory->setDescription(
                    $locale['code'].' The Light Side of the Force was aligned with calmness and was used for knowledge and defense'
                );
                $translationCategory->setParentMultilangue($category);

                $manager->persist($translationCategory);

                $reference = 'translationcategory-'.$locale['code'];
                $this->addReference($reference, $translationCategory);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
