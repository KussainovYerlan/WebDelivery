<?php

namespace App\Repository;

use App\Entity\Product;
use App\Entity\Seller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }


    public function searchProducts(?string $search)
    {
        return $this->createQueryBuilder('p')
            ->Where('p.name LIKE :search')
            ->setParameter('search', $searchName)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findBySeller(Seller $seller, $page = 1, $search)
    {
        $query = $this->createQueryBuilder('p')
            ->andWhere('p.seller = :seller')
            ->andWhere('p.name LIKE :search')
            ->setParameter('seller', $seller->getId())
            ->setParameter('search', '%' . $search . '%')
        ;

        return $this->paginate($query->getQuery(), $page ?: 1);
    }

    public function paginate($dql, $page = 1, $limit = 4)
    {
        $paginator = new Paginator($dql);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);
        return $paginator;
    }
}
