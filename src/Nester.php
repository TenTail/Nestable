<?php

namespace TenTail\Nestable;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Nester implements Arrayable, Jsonable, JsonSerializable
{
    /** @var array */
    private $items = [];

    /** @var null|array */
    private $tree = null;

    public function setItems(array $data): void
    {
        $this->tree = null;

        usort($data, function ($item1, $item2) {
            return $item1['order'] <=> $item2['order'];
        });

        foreach ($data as $item) {
            $parentId = is_null($item['parent_id']) ? 'root' : $item['parent_id'];

            $this->items[(string) $parentId][] = $item;
        }
    }

    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        if (is_null($this->tree)) {
            $this->tree = $this->generateTree();
        }

        return $this->tree;
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * The main logic of nest.
     *
     * @param string $rootId
     *
     * @return array
     */
    private function generateTree(string $rootId = 'root'): array
    {
        if (! array_key_exists($rootId, $this->items)) {
            return [];
        }

        $tree = [];
        foreach ($this->items[$rootId] as $item) {
            $node = $item;
            $node['children'] = $this->generateTree($node['id']);

            $tree[] = $node;
        }

        return $tree;
    }
}
