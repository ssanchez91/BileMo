<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * listAll function
     *
     * @param string $order
     * @param integer $limit
     * @param integer $offset
     * @param [type] $customer
     * @return void
     */
    public function listAll($order = 'desc', $limit = 5, $offset = 1, $customer)
    {
        $qb = $this->createQueryBuilder('u')
            ->join('u.customer', 'c')
            ->where('c.id = :customer')
            ->setParameter('customer', $customer->getId())
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('u.id', $order);

        return new paginator($qb);
    }
}