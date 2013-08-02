<?php

namespace Sirian\FormBundle\Suggest;

interface SuggesterInterface
{
    /**
     * @param $query
     * @param array $options
     * @return Result
     */
    public function suggest($query, $options = []);
    public function transform($items);
    public function reverseTransform($ids);
}
