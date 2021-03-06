<?php
namespace OC\PlatformBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
    public function myFindAll() {
        
         $qb = $this->createQueryBuilder('a');
         
         $this->whereCurentYear($qb);
         
         $qb->orderBy('a.date','DESC');
         
         return  $qb->getQuery()
                    ->getResult();
    }
    
    public function myFindOne($id) {
        
        return $this->createQueryBuilder('a')
                    ->where('a.id = :id')
                    ->setParameter('id', $id)
                    ->getQuery()
                    ->getSingleResult();
    }
    
    public  function whereCurentYear(QueryBuilder $qb) {
        
        $qb->andWhere('a.date BETWEEN :start AND :end')
           ->setParameters([':start' => new \DateTime(date('Y'.'01-01')),
                            ':end' => new \DateTime(date('Y').'12-31')]
               )
        ;
    }
}
