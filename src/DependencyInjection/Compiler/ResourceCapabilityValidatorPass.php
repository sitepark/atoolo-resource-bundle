<?php

namespace Atoolo\Resource\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\LogicException;

class ResourceCapabilityValidatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasParameter('atoolo_resource.resources')) {
            return;
        }

        $resourceDefinitions = $container->getParameter('atoolo_resource.resources');
        // @phpstan-ignore-next-line
        foreach ($resourceDefinitions as $resourceClass => $resourceDefinition) {
            foreach ($resourceDefinition['capabilities'] as $capabilityInterface => $capabilityDefinition) {

                if (!interface_exists($capabilityInterface)) {
                    throw new LogicException(sprintf(
                        'Configuration error: The capability interface "%s" requested for resource "%s" does not exist.',
                        $capabilityInterface,
                        $resourceClass,
                    ));
                }

                $capabilityImplementation = $capabilityDefinition['implementation'];

                try {
                    $reflectionClass = new \ReflectionClass($capabilityImplementation);
                    $constructor = $reflectionClass->getConstructor();


                    if ($constructor === null || $constructor->getNumberOfParameters() === 0) {
                        continue; // No constructor or no parameters, nothing to validate.
                    }

                    $firstParam = $constructor->getParameters()[0];
                    $requiredType = $firstParam->getType();

                    if ($requiredType === null || !$requiredType instanceof \ReflectionNamedType) {
                        continue; // No type-hint, cannot validate.
                    }

                    $requiredResourceClass = $requiredType->getName();

                    // Check if the configured resource is the same as, or a subclass of, the required one.
                    if (!is_a($resourceClass, $requiredResourceClass, true)) {
                        throw new LogicException(sprintf(
                            'Configuration error: The capability implementation "%s" requires '
                                . 'a resource of type "%s", but is configured for "%s".',
                            $capabilityImplementation,
                            $requiredResourceClass,
                            $resourceClass,
                        ));
                    }
                } catch (\ReflectionException $e) {
                    // Could not reflect on the class, might not exist.
                    throw new LogicException(sprintf(
                        'Configuration error: The capability implementation "%s" requested for resource "%s" does not exist.',
                        $capabilityImplementation,
                        $resourceClass,
                    ));
                }
            }
        }
    }
}
