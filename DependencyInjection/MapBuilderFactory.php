<?php

namespace Opportus\ObjectMapperBundle\DependencyInjection;

use Opportus\ObjectMapper\Map\Filter\FilterCollection;
use Opportus\ObjectMapper\Map\MapBuilder;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteBuilderInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;

/**
 * The map builder factory.
 *
 * @package Opportus\ObjectMapperBundle\DependencyInjection
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/ObjectMapperBundle/blob/master/LICENSE MIT
 */
final class MapBuilderFactory
{
    /**
     * Creates a map builder.
     * 
     * @param Opportus\ObjectMapper\Map\Route\RouteBuilderInterface $routeBuilder
     * @param Opportus\ObjectMapper\Map\Route\Route[] $routes
     * @param Opportus\ObjectMapper\Map\Filter\FilterInterface[] $routes
     * @return Opportus\ObjectMapper\Map\MapBuilderInterface[]
     */
    public function createMapBuilder(RouteBuilderInterface $routeBuilder, array $routes, array $filters): MapBuilderInterface
    {
        return new MapBuilder($routeBuilder, new RouteCollection($routes), new FilterCollection($filters));
    }
}
