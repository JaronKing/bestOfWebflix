<?php

namespace Blog\AdminBundle\Entity;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPostsByTime($start, $end)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('m')
            ->from('BlogAdminBundle:Post', 'm')
            ->where('m.dateCreated >= :start')
            ->andWhere('m.dateCreated <= :end')
            ->setParameters( array(
                'start' => $start,
                'end' => $end
            ));
        return $qb->getQuery()->getArrayResult();

    }

    public function findPostsByPage($page, $pager = 10)
    {
        $end = new \DateTime('now');
        $pageResult = ($page - 1) * $pager;
        $qb = $this->_em->createQueryBuilder();
        $qb->select('m')
            ->from('BlogAdminBundle:Post', 'm')
            ->where('m.enabled = true')
            ->andWhere('m.dateCreated <= :end')
            ->setParameters( array(
                'end' => $end
            ))
            ->setMaxResults($pager)
            ->setFirstResult($pageResult)
            ->orderBy('m.dateCreated' , 'DESC');
        return $qb->getQuery()->getArrayResult();
    }
}
