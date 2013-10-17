<?php

namespace tests\Pages\Webservice\Pages;

use Bazalt\Rest\Collection;

class CollectionTest extends \Bazalt\Rest\Test\BaseCase
{
    protected $collection = null;

    public function setUp()
    {
        $this->collection = $this->getMock('Bazalt\\ORM\\Collection', array('addOrderBy', 'clearOrderBy', 'page', 'countPerPage'));
    }

    public function tearDown()
    {
        $this->collection = null;
    }

    public function testExec()
    {
        $table = new Collection($this->collection);

        $table->sortableBy('title')
            ->filterBy('title', function($collection, $value) {
                $collection->andWhere('title LIKE ?', '%' . $value . '%');
            });

        $this->collection->expects($this->any())
            ->method('page')->with($this->equalTo(1));

        $this->collection->expects($this->any())
            ->method('countPerPage')->with($this->equalTo(10));

        $table->exec();

        $collection = clone $this->collection;
        $table = new Collection($collection);

        $collection->expects($this->any())
            ->method('page')->with($this->equalTo(1));

        $collection->expects($this->any())
            ->method('countPerPage')->with($this->equalTo(10));

        $collection->expects($this->any())
            ->method('clearOrderBy');

        $collection->expects($this->any())
            ->method('addOrderBy')
            ->with($this->equalTo('`title` DESC'));

        $params = array(
            'sorting' => array('-title')
        );
        $table->exec($params);
    }
/*
    public function testFetch()
    {
        $pagesCollection = \tests\Model\Page::getCollection();

        $table = new Collection($pagesCollection);

        $table->sortableBy('title');

        $this->createPages([
            [
                'title' => ['en' => 'Test 1']
            ],
            [
                'title' => ['en' => 'Test 2']
            ]
        ]);

        $params = [
            'sorting' => ['-title']
        ];

        $this->assertEquals($table->fetch($params, function($item) {
            return ['title' => $item['title']];
        }), [
            'data' => [
                ['title' => ['en' => 'Test 2', 'orig' => 'en']],
                ['title' => ['en' => 'Test 1', 'orig' => 'en']]
            ],
            'pager' => [
                'current' => 1,
                'count' => 1,
                'total' => 2,
                'countPerPage' => 10
            ]
        ]);

        $params = [
            'sorting' => ['+title']
        ];

        $this->assertEquals($table->fetch($params, function($item) {
            return ['title' => $item['title']];
        }), [
            'data' => [
                ['title' => ['en' => 'Test 1', 'orig' => 'en']],
                ['title' => ['en' => 'Test 2', 'orig' => 'en']]
            ],
            'pager' => [
                'current' => 1,
                'count' => 1,
                'total' => 2,
                'countPerPage' => 10
            ]
        ]);

        $params = [
            'sorting' => [ 'title' => 'desc' ]
        ];

        $this->assertEquals($table->fetch($params, function($item) {
            return ['title' => $item['title']];
        }), [
            'data' => [
                ['title' => ['en' => 'Test 2', 'orig' => 'en']],
                ['title' => ['en' => 'Test 1', 'orig' => 'en']]
            ],
            'pager' => [
                'current' => 1,
                'count' => 1,
                'total' => 2,
                'countPerPage' => 10
            ]
        ]);

        $params = [
            'sorting' => [ 'title' => 'desc' ]
        ];

        $table->sortableBy('title', function($collection, $columnName, $direction) {
            $collection->andWhere($columnName . ' != "Test 2"')
                       ->orderBy($columnName . ' ' . $direction);
        });

        $this->assertEquals($table->fetch($params, function($item) {
            return ['title' => $item['title']];
        }), [
            'data' => [
                ['title' => ['en' => 'Test 1', 'orig' => 'en']]
            ],
            'pager' => [
                'current' => 1,
                'count' => 1,
                'total' => 1,
                'countPerPage' => 10
            ]
        ]);
    }

    public function testFields()
    {
        $pagesCollection = \tests\Model\Page::getCollection();

        $table = new Collection($pagesCollection);

        $table->sortableBy('title');

        $this->createPages([
            [
                'title' => ['en' => 'Test 1']
            ],
            [
                'title' => ['en' => 'Test 2']
            ]
        ]);

        $params = [
            'sorting' => ['-title'],
            'fields' => 'title'
        ];

        $this->assertEquals([
            'data' => [
                ['title' => ['en' => 'Test 2', 'orig' => 'en']],
                ['title' => ['en' => 'Test 1', 'orig' => 'en']]
            ],
            'pager' => [
                'current' => 1,
                'count' => 1,
                'total' => 2,
                'countPerPage' => 10
            ]
        ], $table->fetch($params));
    }*/
}