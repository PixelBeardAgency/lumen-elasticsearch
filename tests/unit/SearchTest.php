<?php

class SearchTest extends \Codeception\TestCase\Test
{

    use \Codeception\Specify;

    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var \Nord\Lumen\Elasticsearch\Search\Search
     */
    protected $search;

    /**
     * @var \Nord\Lumen\Elasticsearch\Search\Query\Compound\BoolQuery
     */
    protected $query;

    /**
     * @var \Nord\Lumen\Elasticsearch\Search\Sort
     */
    protected $sort;


    /**
     * @inheritdoc
     */
    public function _before()
    {
        $service = new \Nord\Lumen\Elasticsearch\ElasticsearchService(\Elasticsearch\ClientBuilder::fromConfig([]));
        $queryBuilder = $service->createQueryBuilder();

        $this->search = $service->createSearch();
        $this->query = $queryBuilder->createBoolQuery();
        $this->query->addMust($queryBuilder->createTermQuery()->setField('field1')->setValue('value1'));

        $sortBuilder = $service->createSortBuilder();
        $this->sort = $service->createSort();
        $this->sort->addSort($sortBuilder->createScoreSort());
    }


    /**
     * Tests setters & getters.
     */
    public function testSetterGetter()
    {
        $this->specify('index can be set and get', function () {
            $this->search->setIndex('index');
            verify($this->search->getIndex())->equals('index');
        });


        $this->specify('type can be set and get', function () {
            $this->search->setType('doc');
            verify($this->search->getType())->equals('doc');
        });


        $this->specify('query can be set and get', function () {
            $this->search->setQuery($this->query);
            verify($this->search->getQuery())->isInstanceOf('\Nord\Lumen\Elasticsearch\Search\Query\Compound\BoolQuery');
        });


        $this->specify('page can be set and get', function () {
            $this->search->setPage(1);
            verify($this->search->getPage())->equals(1);
        });


        $this->specify('size can be set and get', function () {
            $this->search->setSize(100);
            verify($this->search->getSize())->equals(100);
        });


        $this->specify('sort can be set and get', function () {
            $this->search->setSort($this->sort);
            verify($this->search->getSort())->isInstanceOf('\Nord\Lumen\Elasticsearch\Search\Sort');
        });
    }


    /**
     * Tests building the elasticsearch query body.
     */
    public function testBuildBody()
    {
        $this->specify('match all query body page 1', function () {
            $this->search->setPage(1);
            $this->search->setSize(100);
            verify($this->search->buildBody())->equals([
                'query' => ['match_all' => []],
                'size'  => 100,
                'from'  => 0,
            ]);
        });


        $this->specify('match all query body page 2', function () {
            $this->search->setPage(2);
            $this->search->setSize(100);
            verify($this->search->buildBody())->equals([
                'query' => ['match_all' => []],
                'size'  => 100,
                'from'  => 100,
            ]);
        });


        $this->specify('bool query body page 1', function () {
            $this->search->setPage(1);
            $this->search->setSize(100);
            $this->search->setQuery($this->query);
            verify($this->search->buildBody())->equals([
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'term' => ['field1' => 'value1']
                            ]
                        ],
                    ]
                ],
                'size'  => 100,
                'from'  => 0,
            ]);
        });


        $this->specify('match all query with sort body', function () {
            $this->search->setSort($this->sort);
            verify($this->search->buildBody())->equals([
                'query' => ['match_all' => []],
                'sort'  => ['_score'],
                'size'  => 100,
                'from'  => 0,
            ]);
        });
    }
}