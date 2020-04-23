<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Wish;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class WishRepository extends EntityRepository
{
    /**
     * @param string $id
     * @return Wish|null
     * @throws NonUniqueResultException
     */
    public function findById(string $id) : ?Wish
    {
        return $this
            ->createQueryBuilder('wi')
            ->where('wi.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return int
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countAll() : int
    {
        return $this
            ->createQueryBuilder('wi')
            ->select('count(wi.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function findWishesByWishlistId(string $id) : iterable
    {
        $qb = $this
            ->createQueryBuilder('w')
            ->where('w.wishlist = :id')
            ->setParameter('id', $id)
            ->leftJoin('w.image', 'i')
            ->addSelect('partial i.{id, path, thumbnailPath}')
        ;

        return $qb->getQuery()->getResult();
    }
}
