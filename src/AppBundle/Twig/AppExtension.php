<?php

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManager;

class AppExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_category_translation', array($this, 'isCategoryTranslation')),
            new \Twig_SimpleFunction('is_content_translation', array($this, 'isContentTranslation')),
        );
    }

    public function isCategoryTranslation($localeDefault, $localeCategory, $parentMultilangue)
    {
        return $this->em->getRepository('AppBundle:Category')->createQueryBuilder('c')
            ->leftJoin('c.parentMultilangue', 't')
            ->andWhere('c.locale != :localeDefault and c.locale = :localeCategory and t.id = :parentMultilangue')
            ->setParameter('localeDefault', $localeDefault)
            ->setParameter('localeCategory', $localeCategory)
            ->setParameter('parentMultilangue', $parentMultilangue)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function isContentTranslation($localeDefault, $localeContent, $parentMultilangue)
    {
        return $this->em->getRepository('AppBundle:Content')->createQueryBuilder('c')
            ->leftJoin('c.parentMultilangue', 't')
            ->andWhere('c.locale != :localeDefault and c.locale = :localeContent and t.id = :parentMultilangue')
            ->setParameter('localeDefault', $localeDefault)
            ->setParameter('localeContent', $localeContent)
            ->setParameter('parentMultilangue', $parentMultilangue)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getName()
    {
        return 'app.twig.extension';
    }
}