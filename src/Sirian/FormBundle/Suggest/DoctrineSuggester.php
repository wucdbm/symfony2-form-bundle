<?php

namespace Sirian\FormBundle\Suggest;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;

abstract class DoctrineSuggester extends AbstractSuggester
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    public function setDoctrine(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function suggestByFields(array $fields, $query = '', $options = [], $order = [])
    {
        $qb = $this->getQueryBuilder();
        if (isset($options['page']) && $qb->getMaxResults()) {
            $qb->setFirstResult(($options['page'] - 1) * $qb->getMaxResults());
        }
        $this->buildSuggestByFields($qb, $fields, $query, $order);

        return $this->createResult($qb);
    }

    public function buildSuggestByFields(QueryBuilder $qb, array $fields, $query, $order)
    {
        $alias = current($qb->getRootAliases());

        if ($fields) {
            $normalizedQuery = $this->normalizeQuery($query);
            $likes = $qb->expr()->orX();
            foreach ($fields as $field) {
                $parameter = 'suggest_' . $field;
                $where = $qb->expr()->like($alias . '.' . $field, ':' . $parameter);
                $qb->setParameter($parameter, $normalizedQuery, \PDO::PARAM_STR);
                $likes->add($where);
            }

            if ($likes->count()) {
                $qb->andWhere($likes);
            }
        }
        foreach ($order as $field => $direction) {
            $qb->addOrderBy($alias . '.' . $field, $direction);
        }
        return $qb;
    }

    protected function createResult(QueryBuilder $qb)
    {
        $originalLimit = $qb->getMaxResults();

        if ($originalLimit > 0) {
            $qb->setMaxResults($originalLimit + 1);
        }

        $items = $qb->getQuery()->getResult();

        $result = new Result();

        if ($originalLimit > 0 && count($items) > $originalLimit) {
            $result->setHasMore(true);
            $items = array_slice($items, 0, $originalLimit);
        }

        $result->setItems($items);

        return $result;
    }

    protected function normalizeQuery($query)
    {
        return '%' . $query . '%';
    }

    protected function getQueryBuilder()
    {
        return $this
            ->getRepository()
            ->createQueryBuilder('item')
            ->setMaxResults(20)
        ;
    }

    public function reverseTransform($ids)
    {
        return $this->getEntitiesByIds('id', $ids);
    }

    protected function getEntitiesByIds($field, $ids)
    {
        $qb = $this
            ->getQueryBuilder()
            ->setMaxResults(count($ids))
        ;
        $alias = current($qb->getRootAliases());

        $parameter = 'suggest_' . $field;
        $where = $qb->expr()->in($alias . '.' . $field, ':' . $parameter);

        return $qb
            ->andWhere($where)
            ->setParameter($parameter, $ids, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return EntityRepository
     */
    abstract public function getRepository();
}
