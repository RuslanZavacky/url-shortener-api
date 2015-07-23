<?php
namespace Rz\Bundle\UrlShortenerBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Rz\Bundle\UrlShortenerBundle\Entity\UrlStat;

class UrlStatRepository extends EntityRepository
{
    /**
     * Get URL stats count
     *
     * @param int $id
     *
     * @return int
     */
    public function getUrlStatCount($id)
    {
        return (int) $this->createQueryBuilder('c')
            ->select('count(c)')
            ->where('c.Url = :url_id')
            ->setParameter('url_id', $id)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Get URL stats
     *
     * @param int $id
     * @param int $first
     * @param int $max
     *
     * @return array|UrlStat[]
     */
    public function getUrlStats($id, $first, $max)
    {
        return $this->createQueryBuilder('c')
            ->select('c')
            ->where('c.Url = :url_id')
            ->setParameter('url_id', $id)
            ->setFirstResult($first)
            ->setMaxResults($max)
            ->getQuery()
            ->getResult();
    }
} 