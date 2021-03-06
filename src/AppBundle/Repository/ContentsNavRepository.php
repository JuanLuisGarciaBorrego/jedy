<?php

namespace AppBundle\Repository;

/**
 * ContentsNavRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContentsNavRepository extends \Doctrine\ORM\EntityRepository
{
    public function removeNavContentByIdElement($id, $nav)
    {
        $this->createQueryBuilder('cn')
            ->leftJoin('cn.nav', 'nav')
            ->where('cn.nav = :nav and cn.idElement = :id')
            ->setParameter('nav', $nav)
            ->setParameter('id', $id)
            ->delete('AppBundle:ContentsNav', 'cn')
            ->getQuery()
            ->execute()
        ;
    }

    public function getNav($locale)
    {
      $qb = $this->getEntityManager()->createQueryBuilder();
      $qb->select('cn')
       ->from('AppBundle:ContentsNav', 'cn')
       ->innerJoin('AppBundle:Nav', 'n', 'WITH', 'cn.nav = n.id')
       ->where('n.locale = :locale')
       ->andWhere('cn.parentContent IS NULL')
       ->setParameter('locale', $locale)
       ->getQuery();

       return $qb->getQuery()->getResult();
    }

}
