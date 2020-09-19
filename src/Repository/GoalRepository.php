<?php

namespace App\Repository;

use App\Entity\Goal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Goal|null find($id, $lockMode = null, $lockVersion = null)
 * @method Goal|null findOneBy(array $criteria, array $orderBy = null)
 * @method Goal[]    findAll()
 * @method Goal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Goal::class);
    }

    public function findAllUsersGoals($user)
    {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.users', 'u', 'WITH', 'u.id = :user')
            ->setParameter('user', $user)
            ->orderBy('g.sprint', 'DESC')
            ->getQuery()
            ->getResult();
    }

}