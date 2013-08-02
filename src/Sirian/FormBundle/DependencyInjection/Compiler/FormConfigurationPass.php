<?php

namespace Sirian\FormBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class FormConfigurationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $suggester = $container->getDefinition('sirian_form.suggest_registry');

        foreach ($container->findTaggedServiceIds('sirian_form.suggester') as $id => $attributes) {
            foreach ($attributes as $attr) {
                if (!isset($attr['alias'])) {
                    throw new \InvalidArgumentException(sprintf('Suggester "%s" must specify "alias" attribute', $id));
                }

                $suggester->addMethodCall('addSuggesterService', [$attr['alias'], $id]);
            }
        }

        $this->registerFormTheme($container);
    }

    private function registerFormTheme(ContainerBuilder $container)
    {
        $resources = $container->getParameter('twig.form.resources');

        $resources[] = 'SirianFormBundle:Form:form.html.twig';

        $container->setParameter('twig.form.resources', $resources);
    }
}
