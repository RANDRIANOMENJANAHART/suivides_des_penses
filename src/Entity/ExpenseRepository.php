<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function getTotalByCategory(): array
    {
        return $this->createQueryBuilder('e')
            ->select('e.category, SUM(e.amount) as total')
            ->groupBy('e.category')
            ->getQuery()
            ->getResult();
    }
}
