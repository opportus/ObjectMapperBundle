<?php

namespace Opportus\ObjectMapperBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;

/**
 * The object mapper extension.
 *
 * @package Opportus\ObjectMapperBundle\DependencyInjection
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/ObjectMapperBundle/blob/master/LICENSE MIT
 */
class OpportusObjectMapperExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.xml');

        $container->setAlias('Opportus\ObjectMapper\ClassCanonicalizerInterface', 'opportus_object_mapper.class_canonicalizer');
        $container->setAlias('Opportus\ObjectMapper\Map\Route\Point\PointFactoryInterface', 'opportus_object_mapper.map.route.point.point_factory');
        $container->setAlias('Opportus\ObjectMapper\Map\Route\RouteBuilderInterface', 'opportus_object_mapper.map.route.route_builder');
        $container->setAlias('Opportus\ObjectMapper\Map\Definition\MapDefinitionRegistryInterface', 'opportus_object_mapper.map.definition.map_definition_registry');
        $container->setAlias('Opportus\ObjectMapper\Map\Definition\MapDefinitionBuilderInterface', 'opportus_object_mapper.map.definition.map_definition_builder');
        $container->setAlias('Opportus\ObjectMapper\Map\MapBuilderInterface', 'opportus_object_mapper.map.map_builder');
        $container->setAlias('Opportus\ObjectMapper\ObjectMapperInterface', 'opportus_object_mapper.object_mapper');
    }
}

