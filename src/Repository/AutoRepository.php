<?php

namespace App\Repository;

use App\Entity\Auto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Auto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Auto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Auto[]    findAll()
 * @method Auto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AutoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auto::class);
    }

    // /**
    //  * @return Auto[] Returns an array of Autos objects
    //  */    
    /*
    public function findByExampleField(string $marca)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */
    

    /*
    public function findOneBySomeField($value): ?Users
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    // /**
    //  * @return Auto[] Returns an array of Autos objects
    //  */    
    public function findAutosPropietarios($params, $metodo='orWhere')
    {   
        $query= $this->createQueryBuilder('a');        

        foreach($params as $index=>$valor){
            $campo = $valor[0];
            $operador = $valor[1];
            $value = $valor[2];

            switch($metodo){
                case 'andWhere':$query->andwhere('a.'.$campo." $operador :".$campo);break;
                default:$query->orWhere('a.'.$campo." $operador :".$campo);break;
            }

            switch($operador){
                case 'LIKE':$query->setParameter($campo, '%'. $value. '%');break;
                default: $query->setParameter($campo, $value);break;
            }
        }

        $result = $query->orderBy('a.marca', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $result;
    }

    // /**
    //  * @return Auto[] Returns an array of Autos objects
    //  */    
    public function findAllAutosPropietarios()
    {        
        $query= $this->createQueryBuilder('a');
        $result = $query->orderBy('a.marca', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
