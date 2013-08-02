<?php

namespace Sirian\FormBundle\Suggest;

use Symfony\Component\DependencyInjection\ContainerInterface;

class Registry
{
    protected $container;
    protected $suggesters = [];
    protected $suggesterServices = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function addSuggester($name, SuggesterInterface $suggester)
    {
        $this->suggesters[$name] = $suggester;
    }

    public function addSuggesterService($name, $suggesterService)
    {
        $this->suggesterServices[$name] = $suggesterService;
    }

    public function hasSuggester($name)
    {
        return isset($this->suggesters[$name]) || isset($this->suggesterServices[$name]);
    }

    /**
     * @param $name
     * @return SuggesterInterface
     * @throws \InvalidArgumentException
     */
    public function getSuggester($name)
    {
        if (isset($this->suggesters[$name])) {
            return $this->suggesters[$name];
        }

        if (isset($this->suggesterServices[$name])) {
            $this->suggesters[$name] = $this->container->get($this->suggesterServices[$name]);
            return $this->suggesters[$name];
        }

        throw new \InvalidArgumentException(sprintf('Suggester "%" not registered', $name));
    }
}
