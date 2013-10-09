<?php

namespace Sirian\FormBundle\Suggest;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class BaseDoctrineSuggester extends \Sirian\FormBundle\Suggest\DoctrineSuggester
{
    protected $repositoryName;
    protected $managerName;
    protected $searchFields = [];
    protected $paths = [];
    protected $order = [];

    public function __construct($repositoryName, $managerName = null, $searchFields = ['name'], $paths = array(), $order = [])
    {
        $this->repositoryName = $repositoryName;
        $this->managerName = $managerName;
        $this->searchFields = $searchFields;
        $this->paths = $paths;
        $this->order = $order;
    }

    public function getRepository()
    {
        return $this->doctrine->getRepository($this->repositoryName, $this->managerName);
    }

    public function suggest($query, $options = [])
    {
        return $this->suggestByFields($this->searchFields, $query, $options, $this->order);
    }

    public function transform($items)
    {
        $propertyAccessor = new PropertyAccessor();

        return array_map(function($entity) use ($propertyAccessor) {
            $data = [];
            foreach ($this->paths as $key => $path) {
                $data[$key] = $propertyAccessor->getValue($entity, $path);
            }
            return $data;
        }, $items);
    }
}
