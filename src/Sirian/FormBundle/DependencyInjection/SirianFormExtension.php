<?php

namespace Sirian\FormBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class SirianFormExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        foreach ($config['suggest'] as $key => $suggest) {
            $definition = new DefinitionDecorator('sirian_form.base_doctrine_suggester');

            $definition
                ->addArgument($suggest['entity'])
                ->addArgument($suggest['manager'])
                ->addArgument($suggest['search'])
                ->addArgument($suggest['paths'] ?: [
                    'id' => 'id',
                    'text' => 'name'
                ])
                ->addArgument($suggest['order'])
                ->addTag('sirian_form.suggester', [
                    'alias' => $key
                ])
            ;
            $id = 'sirian_form.base_doctrine_suggester.' . $key;

            $container->setDefinition($id, $definition);
        }
    }
}
