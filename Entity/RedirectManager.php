<?php

namespace Rz\RedirectBundle\Entity;

use Sonata\CoreBundle\Model\BaseEntityManager;
use Rz\RedirectBundle\Model\RedirectManagerInterface;

class RedirectManager extends BaseEntityManager implements RedirectManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findEnableRedirect(array $criteria)
    {
        $date = new \Datetime();
        $parameters = array(
            'publicationDateStart' => $date,
            'publicationDateEnd'   => $date,
        );

        $query = $this->getRepository()
            ->createQueryBuilder('r')
            ->andWhere('r.publicationDateStart <= :publicationDateStart AND ( r.publicationDateEnd IS NULL OR r.publicationDateEnd >= :publicationDateEnd )');

        if (isset($criteria['fromPath'])) {
            $query->andWhere('r.fromPath = :fromPath');
            $parameters['fromPath'] = $criteria['fromPath'];
        } else {
            throw new \RuntimeException('please provide a `fromPath` as criteria key');
        }

        $query->setMaxResults(1);
        $query->setParameters($parameters);

        return $query->getQuery()
            ->useResultCache(true, 3600)
            ->getOneOrNullResult();
    }

    /**
     * {@inheritdoc}
     */
    public function fixOldRedirects(array $criteria)
    {
        if (!isset($criteria['referenceId']) && !isset($criteria['type'])  && !isset($criteria['toPath'])  && !isset($criteria['currentId']) ) {
            throw new \RuntimeException('please provide a `referenceId`, `type`, `toPath` and `currentId` as criteria key');
        } else {

        }

        $this->getEntityManager()->flush();
        //@todo: strange sql and low-level pdo usage: use dql or qb
        $sql = sprintf("UPDATE %s SET to_path = '%s' WHERE reference_id = %s AND type = '%s' AND id != %s",
            $this->getTableName(),
            $criteria['toPath'],
            $criteria['referenceId'],
            $criteria['type'],
            $criteria['currentId']
        );

        $this->getConnection()->query($sql);
    }
}