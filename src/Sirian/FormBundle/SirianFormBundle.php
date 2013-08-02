<?php

namespace Sirian\FormBundle;

use Sirian\FormBundle\DependencyInjection\Compiler\FormConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SirianFormBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FormConfigurationPass());
    }
}
