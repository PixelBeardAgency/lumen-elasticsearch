<?php namespace Nord\Lumen\Elasticsearch\Search\Query\Joining;

use Nord\Lumen\Elasticsearch\Exceptions\InvalidArgument;
use Nord\Lumen\Elasticsearch\Search\Query\Traits\HasType;

/**
 * The has_child filter accepts a query and the child type to run against, and results in parent documents that have
 * child docs matching the query.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-child-query.html
 */
class HasChildQuery extends AbstractQuery
{
    use HasType;

    /**
     * @var ?int
     */
    private $minChildren;

    /**
     * @var ?int
     */
    private $maxChildren;

    /**
     * @inheritdoc
     * @throws InvalidArgument
     */
    public function toArray()
    {
        $query = $this->getQuery();
        
        if ($query === null) {
            throw new InvalidArgument('Query must be set');
        }
        
        $hasChild = [
            'type'  => $this->getType(),
            'query' => $query->toArray(),
        ];

        $scoreMode = $this->getScoreMode();
        if (null !== $scoreMode) {
            $hasChild['score_mode'] = $scoreMode;
        }

        $minChildren = $this->getMinChildren();
        if (null !== $minChildren) {
            $hasChild['min_children'] = $minChildren;
        }

        $maxChildren = $this->getMaxChildren();
        if (null !== $maxChildren) {
            $hasChild['max_children'] = $maxChildren;
        }

        return ['has_child' => $hasChild];
    }

    /**
     * @param int $minChildren
     * @return HasChildQuery
     */
    public function setMinChildren(int $minChildren)
    {
        $this->minChildren = $minChildren;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getMinChildren(): ?int
    {
        return $this->minChildren;
    }


    /**
     * @param int $maxChildren
     * @return HasChildQuery
     */
    public function setMaxChildren(int $maxChildren)
    {
        $this->maxChildren = $maxChildren;
        return $this;
    }


    /**
     * @return int|null
     */
    public function getMaxChildren(): ?int
    {
        return $this->maxChildren;
    }
}
