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
            AND r.is_active = 1
            ORDER BY RAND() 
            LIMIT ' . (int) $limit;

        $resultSet = $conn->executeQuery($sql, [
            'rate' => $rate,
            'limit' => $limit,
        ]);

        return $resultSet->fetchAllAssociative();
    }

    public function findRandomRecipesByCategory(int $categoryId, int $limit = 3): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT r.* FROM recipe r
            INNER JOIN recipe_category rc ON r.id = rc.recipe_id
            WHERE rc.category_id = :categoryId
            AND r.is_active = 1
            ORDER BY RAND()
            LIMIT ' . (int) $limit;

        $resultSet = $conn->executeQuery($sql, [
            'categoryId' => $categoryId
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
