<?php

namespace Opportus\ObjectMapperBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The mapping predefinition before optimization pass.
 *
 * @package Opportus\ObjectMapperBundle\DependencyInjection
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/ObjectMapperBundle/blob/master/LICENSE MIT
 */
final class MappingPredefinitionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filters = [];

        foreach ($container->findTaggedServiceIds('object_mapper.filter') as $filterId => $filterTags) {
            $filters[] = new Reference($filterId);
        }

        $container->getDefinition('opportus_object_mapper.map_builder')->setArgument(1, []);
        $container->getDefinition('opportus_object_mapper.map_builder')->setArgument(2, $filters);
    }
}
