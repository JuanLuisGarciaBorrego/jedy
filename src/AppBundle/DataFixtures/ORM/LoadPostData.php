<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Content;

class LoadPostData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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

        $post = new Content($locales->getLocaleActive());
        $post->setTitle('Jedi Master');
        $post->setSummary('Jedi Master was a rank in the Jedi Order given to powerful Jedi, many of whom were leaders within the Jedi Order.');
        $post->setContent('Yoda was one of the oldest Jedi Masters, having lived approximately 900 years before his death. Over the course of his 800-year-long career as a Jedi Master, Yoda trained no less than 20,000 Jedi.');
        $post->setType('post');
        $post->setStatus(true);
        $post->setCategory($this->getReference('category'));

        $manager->persist($post);

        foreach ($locales->getLocales() as $locale) {

            if (!$locale['active']) {
                $translationPost = new Content($locale['code']);
                $translationPost->setTitle($locale['code'].' Jedi Master');
                $translationPost->setSummary($locale['code'].' Jedi Master was a rank in the Jedi Order given to powerful Jedi, many of whom were leaders within the Jedi Order.');
                $translationPost->setContent($locale['code'].' Yoda was one of the oldest Jedi Masters, having lived approximately 900 years before his death. Over the course of his 800-year-long career as a Jedi Master, Yoda trained no less than 20,000 Jedi.');
                $translationPost->setType('post');
                $translationPost->setStatus(true);
                $translationPost->setCategory($this->getReference('translationcategory-'.$locale['code']));
                $translationPost->setParentMultilangue($post);

                $manager->persist($translationPost);
            }
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}