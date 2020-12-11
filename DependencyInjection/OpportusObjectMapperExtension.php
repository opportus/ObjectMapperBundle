<?php

namespace Opportus\ObjectMapperBundle\DependencyInjection;

use Opportus\ObjectMapper\ObjectMapperInterface;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Point\CheckPointInterface;
use Opportus\ObjectMapper\Point\PointFactoryInterface;
use Opportus\ObjectMapper\Route\RouteBuilderInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * The object mapper extension.
 *
 * @package Opportus\ObjectMapperBundle\DependencyInjection
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/ObjectMapperBundle/blob/master/LICENSE MIT
 */
final class OpportusObjectMapperExtension extends Extension
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

        $container->setAlias(PointFactoryInterface::class, 'opportus_object_mapper.point_factory');
        $container->setAlias(RouteBuilderInterface::class, 'opportus_object_mapper.route_builder');
        $container->setAlias(MapBuilderInterface::class, 'opportus_object_mapper.map_builder');
        $container->setAlias(ObjectMapperInterface::class, 'opportus_object_mapper.object_mapper');

        $container->registerForAutoconfiguration(PathFinderInterface::class)->addTag('opportus_object_mapper.path_finder');
        $container->registerForAutoconfiguration(CheckPointInterface::class)->addTag('opportus_object_mapper.check_point');
    }
}
