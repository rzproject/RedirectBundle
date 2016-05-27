<?php

namespace Rz\RedirectBundle\Entity;

use Sonata\CoreBundle\Model\BaseEntityManager;
use Rz\RedirectBundle\Model\RedirectManagerInterface;

class RedirectManager extends BaseEntityManager implements RedirectManagerInterface
{

    protected $redirectTypes = [];
    protected $defaultRedirect;

    /**
     * @return array
     */
    public function getRedirectTypes()
    {
        return $this->redirectTypes;
    }

    /**
     * @param array $redirectTypes
     */
    public function setRedirectTypes($redirectTypes)
    {
        $this->redirectTypes = $redirectTypes;
    }

    /**
     * @return mixed
     */
    public function getDefaultRedirect()
    {
        return $this->defaultRedirect;
    }

    /**
     * @param mixed $defaultRedirect
     */
    public function setDefaultRedirect($defaultRedirect)
    {
        $this->defaultRedirect = $defaultRedirect;
    }

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
    public function findRedirectsByReferenceId($referenceId, $currentId)
    {
        if(!$referenceId || !$currentId ) {
            throw new \RuntimeException('please provide a `referenceId` and `currentId`');
        }

        $parameters = ['referenceId' => $referenceId, 'currentId' => $currentId];

        $query = $this->getRepository()
            ->createQueryBuilder('r')
            ->andWhere('r.publicationDateEnd IS NULL AND r.referenceId = :referenceId AND r.id != :currentId');

        $query->setParameters($parameters);

        return $query->getQuery()
            ->useResultCache(true, 3600)
            ->getResult();
    }

    /**
     * {@inheritdoc}
     */
    public function fixOldRedirects(array $criteria)
    {

        if (!isset($criteria['referenceId']) && !isset($criteria['type']) && !isset($criteria['toPath']) && !isset($criteria['currentId'])) {
            throw new \RuntimeException('please provide a `referenceId`, `type`, `toPath` and `currentId` as criteria key');
        }

        //find all old redirects
        $redirects = $this->findRedirectsByReferenceId($criteria['referenceId'], $criteria['currentId']);

        if (count($redirects) == 0) {
            return;
        }

        //update all current redirects
        $publicationDateStart = $publicationDateEnd = new \Datetime();
        $redirectIds = [];

        foreach ($redirects as $redirect) {
            $redirectIds[] = $redirect->getId();
            $newRedirect = $this->create();
            $newRedirect->setName($redirect->getName());
            $newRedirect->setEnabled($redirect->getEnabled());
            $newRedirect->setType($redirect->getType());
            $newRedirect->setReferenceId($redirect->getReferenceId());
            $newRedirect->setFromPath($redirect->getFromPath());
            $newRedirect->setRedirect($criteria['redirect']);
            $newRedirect->setToPath($criteria['toPath']);
            $newRedirect->setPublicationDateStart($publicationDateStart);
            $newRedirect->setPublicationDateEnd(null);
            $this->getEntityManager()->persist($newRedirect);
        }

        $this->getEntityManager()->flush();

        //@todo: strange sql and low-level pdo usage: use dql or qb
        $sql = sprintf("UPDATE %s SET publication_date_end = '%s' WHERE id IN(%s)",
            $this->getTableName(),
            $publicationDateEnd->format('Y-m-d H:i:s'),
            implode(',', $redirectIds)
        );

        $this->getConnection()->query($sql);
    }
}