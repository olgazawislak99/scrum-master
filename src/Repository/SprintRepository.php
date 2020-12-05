<?php


namespace App\Repository;

use App\Entity\Sprint;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sprint|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sprint|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sprint[]    findAll()
 * @method Sprint[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SprintRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sprint::class);
    }

    public function findActualWeekSprint(string $firstDayOfWeek, string $lastDayOfWeek, $project)
    {
        return $this->createQueryBuilder('s')
            ->where('s.project = (:project)')
            ->andWhere("s.start_date >= :first AND  s.end_date <= :last")
            ->setParameter('first', $firstDayOfWeek)
            ->setParameter('last', $lastDayOfWeek)
            ->setParameter('project', $project)
            ->getQuery()
            ->getOneOrNullResult();
    }

}