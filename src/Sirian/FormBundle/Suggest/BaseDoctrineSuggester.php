<?php

namespace Sirian\FormBundle\Suggest;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class BaseDoctrineSuggester extends \Sirian\FormBundle\Suggest\DoctrineSuggester
{
    protected $repositoryName;
    protected $managerName;
    protected $searchFields = [];
    protected $idPath;
    protected $textPath;

    public function __construct($repositoryName, $managerName = null, $searchFields = ['name'], $idPath = 'id', $textPath = 'name')
    {
        $this->repositoryName = $repositoryName;
        $this->managerName = $managerName;
        $this->searchFields = $searchFields;
        $this->idPath = $idPath;
        $this->textPath = $textPath;
    }

    public function getRepository()
    {
        return $this->doctrine->getRepository($this->repositoryName, $this->managerName);
    }

    public function suggest($query, $options = [])
    {
        return $this->suggestByFields($this->searchFields, $query, $options);
    }

    public function transform($items)
    {
        $propertyAccessor = new PropertyAccessor();

        return array_map(function($campaign) use ($propertyAccessor) {
            return [
                'id' => $propertyAccessor->getValue($campaign, $this->idPath),
                'text' => $propertyAccessor->getValue($campaign, $this->textPath)
            ];
        }, $items);
    }
}
