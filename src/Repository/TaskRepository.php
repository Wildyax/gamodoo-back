<?php

namespace App\Repository;

use App\Entity\Tag;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    //    /**
    //     * @return Task[] Returns an array of Task objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Task
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    /**
     * Get tasks by filter list
     * @param array $filters Filters list
     * @return mixed|array
     */
    public function findByFilters(array $filters): array
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.user = :user')
            ->setParameter('user', $filters['user']);

        // Filtre par label (LIKE pour une recherche partielle)
        if (!empty($filters['label'])) {
            $qb->andWhere('t.label LIKE :label')
                ->setParameter('label', '%' . $filters['label'] . '%');
        }

        // Filtre par statut "checked"
        if (is_bool($filters['checked'])) {
            $qb->andWhere('t.checked = :checked')
                ->setParameter('checked', $filters['checked']);
        }

        // Filtre par tags (si au moins un tag est spécifié)
        if (!empty($filters['tags'])) {
            $qb->join('t.tags', 'tag')
                ->andWhere('tag.label IN (:tags)')
                ->setParameter('tags', $filters['tags']);
        }

        return $qb->getQuery()->getResult();
    }
}
