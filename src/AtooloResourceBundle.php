<?php

declare(strict_types=1);

namespace Atoolo\Resource;

use Atoolo\Resource\DependencyInjection\Compiler\ResourceCapabilityValidatorPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @codeCoverageIgnore
 */
class AtooloResourceBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new ResourceCapabilityValidatorPass());
    }
}
