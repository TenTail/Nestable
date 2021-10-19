<?php

namespace TenTail\Nestable\Tests;

use TenTail\Nestable\Nester;

class NesterTest extends TestCase
{
    protected $data = [
        ['id' => 1, 'parent_id' => null, 'name' => 'Item 1', 'order' => 2],
        ['id' => 2, 'parent_id' => null, 'name' => 'Item 2', 'order' => 0],
        ['id' => 3, 'parent_id' => null, 'name' => 'Item 3', 'order' => 1],
        ['id' => 4, 'parent_id' => 3, 'name' => 'Item 4', 'order' => 0],
        ['id' => 5, 'parent_id' => 4, 'name' => 'Item 5', 'order' => 0],
        ['id' => 6, 'parent_id' => 4, 'name' => 'Item 6', 'order' => 1],
    ];

    /**
     * @test
     */
    public function testSetItems()
    {
        $excepted = [
            'root' => [
                ['id' => 2, 'parent_id' => null, 'name' => 'Item 2', 'order' => 0],
                ['id' => 3, 'parent_id' => null, 'name' => 'Item 3', 'order' => 1],
                ['id' => 1, 'parent_id' => null, 'name' => 'Item 1', 'order' => 2],
            ],
            '3' => [
                ['id' => 4, 'parent_id' => 3, 'name' => 'Item 4', 'order' => 0],
            ],
            '4' => [
                ['id' => 5, 'parent_id' => 4, 'name' => 'Item 5', 'order' => 0],
                ['id' => 6, 'parent_id' => 4, 'name' => 'Item 6', 'order' => 1],
            ],
        ];

        $nester = new Nester();
        $nester->setItems($this->data);

        $this->assertEquals($excepted, $nester->getItems());
    }

    /**
     * @test
     */
    public function testJsonSerialized()
    {
        $exceptedTree = [
            [
                'id' => 2,
                'parent_id' => null,
                'name' => 'Item 2',
                'order' => 0,
                'children' => [],
            ],
            [
                'id' => 3,
                'parent_id' => null,
                'name' => 'Item 3',
                'order' => 1,
                'children' => [
                    [
                        'id' => 4,
                        'parent_id' => 3,
                        'name' => 'Item 4',
                        'order' => 0,
                        'children' => [
                            [
                                'id' => 5,
                                'parent_id' => 4,
                                'name' => 'Item 5',
                                'order' => 0,
                                'children' => [],
                            ],
                            [
                                'id' => 6,
                                'parent_id' => 4,
                                'name' => 'Item 6',
                                'order' => 1,
                                'children' => [],
                            ],
                        ]
                    ],
                ]
            ],
            [
                'id' => 1,
                'parent_id' => null,
                'name' => 'Item 1',
                'order' => 2,
                'children' => [],
            ],
        ];

        $excepted = json_encode($exceptedTree);

        $nester = new Nester();
        $nester->setItems($this->data);

        $this->assertEquals($exceptedTree, $nester->toArray());
        $this->assertEquals($excepted, $nester->toJson());
    }
}
