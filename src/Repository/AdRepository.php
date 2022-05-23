<?php

namespace App\Repository;

use App\Entity\Ad;
use App\Lib\Pagination;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @extends ServiceEntityRepository<Ad>
 *
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Ad $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Ad $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function getPagination(UrlGeneratorInterface $router, int $page): Pagination
    {
        $queryBuilder = $this->dbalListQueryBuilder();
        return new Pagination($queryBuilder, $router, $page);
    }

    protected function dbalListQueryBuilder(): QueryBuilder
    {
        $queryBuilder = $this->dbalQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('ad', 'a');
        return $queryBuilder;
    }

    protected function dbalQueryBuilder(): QueryBuilder
    {
        return $this->_em->getConnection()->createQueryBuilder();
    }
}
