<?php

namespace AppBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Asset\Packages;

class AppAdminExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Packages
     */
    private $packages;

    private $uploads_directory_name;

    public function __construct(EntityManager $em, Packages $packages, $uploads_directory_name)
    {
        $this->em = $em;
        $this->packages = $packages;
        $this->uploads_directory_name = $uploads_directory_name;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_category_translation', array($this, 'isCategoryTranslation')),
            new \Twig_SimpleFunction('is_content_translation', array($this, 'isContentTranslation')),
            new \Twig_SimpleFunction('is_nav_translation', array($this, 'isNavTranslation')),
            new \Twig_SimpleFunction('render_file', [$this, 'render_file'], ['is_safe' => ['html'] ] ),
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

    public function isNavTranslation($localeDefault, $localeContent, $parentMultilangue)
    {
        return $this->em->getRepository('AppBundle:Nav')->createQueryBuilder('n')
            ->leftJoin('n.parentMultilangue', 't')
            ->andWhere('n.locale != :localeDefault and n.locale = :localeContent and t.id = :parentMultilangue')
            ->setParameter('localeDefault', $localeDefault)
            ->setParameter('localeContent', $localeContent)
            ->setParameter('parentMultilangue', $parentMultilangue)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function render_file($filename, $extension)
    {
         $img = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'pjpeg'];

         if (in_array($extension, $img)) {
             $path = $this->uploads_directory_name."/".$filename;
             return '<img src="'.$this->packages->getUrl($path).'" height="42">';
         }
    }

    public function getName()
    {
        return 'app_admin.twig.extension';
    }
}