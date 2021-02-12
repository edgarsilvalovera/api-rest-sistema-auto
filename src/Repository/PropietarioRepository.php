<?php

namespace App\Repository;

use App\Entity\Propietario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Propietario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Propietario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Propietario[]    findAll()
 * @method Propietario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropietarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Propietario::class);
    }

    // /**
    //  * @return Propietario[] Returns an array of Propietarios objects
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
    //  * @return Propietario[] Returns an array of Propietarios objects
    //  */    
    public function findPropietarios($params, $metodo='orWhere')
    {        
        $query= $this->createQueryBuilder('p');        

        foreach($params as $index=>$valor){
            $campo = $valor[0];
            $operador = $valor[1];
            $value = $valor[2];

            switch($metodo){
                case 'andWhere':$query->andwhere('p.'.$campo." $operador :".$campo);break;
                default:$query->orWhere('p.'.$campo." $operador :".$campo);break;
            }

            switch($operador){
                case 'LIKE':$query->setParameter($campo, '%'. $value. '%');break;
                default: $query->setParameter($campo, $value);break;
            }
        }

        $result = $query->orderBy('p.apellido', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();
            
        return $result;
    }

    // /**
    //  * @return Propietario[] Returns an array of Propietarios objects
    //  */    
    public function findAllPropietarios()
    {        
        $query= $this->createQueryBuilder('p');
        $result = $query->orderBy('p.apellido', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
