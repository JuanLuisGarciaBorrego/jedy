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
        );
    }

    public function isCategoryTranslation($localeDefault, $localeCategory, $parentMultilangue)
    {
        $query = $this->em->getRepository('AppBundle:Category')->createQueryBuilder('c')
            ->addSelect('c.id')
            ->leftJoin('c.parentMultilangue', 't')
            ->andWhere('c.locale != :localeDefault and c.locale = :localeCategory and t.id = :parentMultilangue')
            ->setParameter('localeDefault', $localeDefault)
            ->setParameter('localeCategory', $localeCategory)
            ->setParameter('parentMultilangue', $parentMultilangue)
            ->getQuery()
            ->getOneOrNullResult();

        return ($query) ? true : false;
    }

    public function getName()
    {
        return 'app.twig.extension';
    }
}