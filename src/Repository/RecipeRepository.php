<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    public function findMaxRate(): ?int
    {
        return $this->createQueryBuilder('r')
            ->select('MAX(r.rate) as maxRate')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findRandomRecipeByRate(int $rate, int $limit = 3): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT * FROM recipe r 
            WHERE r.rate = :rate 
            ORDER BY RAND() 
            LIMIT ' . (int) $limit;

        $resultSet = $conn->executeQuery($sql, [
            'rate' => $rate,
            'limit' => $limit,
        ]);

        return $resultSet->fetchAllAssociative();
    }

    // public function findRandomRecipeByRate(int $rate, int $limit = 3): array
    // {
    //     return $this->createQueryBuilder('r')
    //         ->where('r.rate = :rate')
    //         ->setParameter('rate', $rate)
    //         ->orderBy('RAND()')
    //         ->setMaxResults($limit)
    //         ->getQuery()
    //         ->getResult();
    // }
}
