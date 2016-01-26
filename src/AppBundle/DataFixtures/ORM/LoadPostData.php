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
        $this->createObject(
            $manager,
            'Jedi Master',
            'Jedi Master was a rank in the Jedi Order given to powerful Jedi, many of whom were leaders within the Jedi Order.',
            'Yoda was one of the oldest Jedi Masters, having lived approximately 900 years before his death. Over the course of his 800-year-long career as a Jedi Master, Yoda trained no less than 20,000 Jedi.'
        );

        $this->createObject(
            $manager,
            'Luke Skywalker',
            'Luke Skywalker was a Force-sensitive Human male Jedi Master.',
            'He was the son of fallen Jedi Knight Anakin Skywalker and Senator Padmé Amidala, the grandson of Shmi Skywalker Lars, the step-nephew to Owen and Beru Lars, the twin brother of Leia Organa and uncle of Ben. Born on the asteroid Polis Massa, Luke\'s mother died in labor, and the twins were thought to never have been born by his father, the recently christened Sith Lord Darth Vader.'
        );

        $this->createObject(
            $manager,
            'Obi-Wan Kenobi',
            'Obi-Wan Kenobi, later known as Ben Kenobi, was a Force-sensitive human male Jedi Master who served the Galactic Republic.',
            'He was mentor to both Anakin Skywalker and his son, Luke, training them in the ways of the Force. Born on the planet Stewjon, Kenobi was taken as the Padawan learner of Qui-Gon Jinn. During the Invasion of Naboo, Kenobi became the first Jedi in a millennium to defeat a Sith Lord when he defeated Darth Maul during the Battle of Naboo, but in that battle, Maul mortally wounded Jinn.'
        );

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }

    private function createObject(ObjectManager $manager, $title, $summary, $content)
    {
        $locales = $this->container->get('locales');

        $post = new Content($locales->getLocaleActive());
        $post->setTitle($title);
        $post->setSummary($summary);
        $post->setContent($content);
        $post->setType('post');
        $post->setStatus(true);
        $post->setCategory($this->getReference('category'));

        $manager->persist($post);

        foreach ($locales->getLocales() as $locale) {
            if (!$locale['active']) {
                $translationPost = new Content($locale['code']);
                $translationPost->setTitle($locale['code'].' '.$title);
                $translationPost->setSummary($locale['code'].' '.$summary);
                $translationPost->setContent($locale['code'].' '.$content);
                $translationPost->setType('post');
                $translationPost->setStatus(true);
                $translationPost->setCategory($this->getReference('translationcategory-'.$locale['code']));
                $translationPost->setParentMultilangue($post);

                $manager->persist($translationPost);
            }
        }
    }
}
