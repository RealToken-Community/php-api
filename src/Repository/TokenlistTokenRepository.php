<?php

namespace App\Repository;

use App\Entity\TokenlistToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TokenlistToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method TokenlistToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method TokenlistToken[]    findAll()
 * @method TokenlistToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TokenlistTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TokenlistToken::class);
    }

    /**
     * Get findAll() in array.
     *
     * @return array
     */
    public function findAllArrayResponse(): array
    {
        $query = $this->_em
            ->getRepository(TokenlistToken::class)
            ->createQueryBuilder('t')
            ->getQuery();
        return $query->getArrayResult();
    }
}
