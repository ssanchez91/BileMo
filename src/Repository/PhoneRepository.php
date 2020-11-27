<?php

namespace App\Repository;

use App\Entity\Phone;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Phone|null find($id, $lockMode = null, $lockVersion = null)
 * @method Phone|null findOneBy(array $criteria, array $orderBy = null)
 * @method Phone[]    findAll()
 * @method Phone[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Phone::class);
    }

    public function listAll($term, $order = 'asc', $limit = 5, $offset = 1)
    {
        $qb = $this->createQueryBuilder('p')
            ->setFirstResult(($offset - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('p.brand', $order);

        if ($term) {
            $qb
                ->where('p.brand LIKE ?1')
                ->setParameter(1, '%' . $term . '%');
        }

        return new paginator($qb);
    }
}
