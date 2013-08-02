<?php

namespace Sirian\FormBundle\Suggest;

class Result
{
    protected $items;
    protected $hasMore = false;

    public function __construct($items = [])
    {
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function setItems($items)
    {
        $this->items = $items;
        return $this;
    }

    public function toArray()
    {
        return [
            'items' => $this->getItems(),
            'hasMore' => $this->hasMore
        ];
    }

    public function hasMore()
    {
        return $this->hasMore;
    }

    public function setHasMore($more)
    {
        $this->hasMore = !!$more;
    }
}
