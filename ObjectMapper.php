<?php

namespace Opportus\ObjectMapperBundle;

use Opportus\ObjectMapper\ObjectMapperInterface;
use Opportus\ObjectMapper\ClassCanonicalizerInterface;
use Opportus\ObjectMapper\Map\MapBuilderInterface;
use Opportus\ObjectMapper\Map\MapInterface;
use Opportus\ObjectMapper\Map\Route\RouteCollection;
use Opportus\ObjectMapper\Map\Route\Point\PropertyPoint;
use Opportus\ObjectMapper\Map\Route\Point\ParameterPoint;
use Opportus\ObjectMapper\Exception\InvalidArgumentException;
use Opportus\ObjectMapperBundle\Event\TargetPointValueAssignmentEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The object mapper.
 *
 * @package Opportus\ObjectMapperBundle
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/ObjectMapperBundle/blob/master/LICENSE MIT
 */
final class ObjectMapper implements ObjectMapperInterface
{
    /**
     * @var Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     */
    private $classCanonicalizer;

    /**
     * @var Opportus\ObjectMapper\Map\MapBuilderInterface $mapBuilder
     */
    private $mapBuilder;

    /**
     * @var Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    private $eventDispatcher;

    /**
     * Constructs the object mapper.
     *
     * @param Opportus\ObjectMapper\ClassCanonicalizerInterface $classCanonicalizer
     * @param Opportus\ObjectMapper\Map\MapBuilderInterface $mapBuilder
     * @param Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ClassCanonicalizerInterface $classCanonicalizer, MapBuilderInterface $mapBuilder, EventDispatcherInterface $eventDispatcher)
    {
        $this->classCanonicalizer = $classCanonicalizer;
        $this->mapBuilder = $mapBuilder;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getMapBuilder(): MapBuilderInterface
    {
        return $this->mapBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function map(object $source, $target, ?MapInterface $map = null): ?object
    {
        if (!\is_string($target) && !\is_object($target)) {
            throw new InvalidArgumentException(\sprintf(
                'Argument "target" passed to "%s" is invalid. Expects an argument of type object or string, got an argument of type "%s".',
                __METHOD__,
                \gettype($target)
            ));
        }

        $map = $map ?? $this->mapBuilder->buildMap();
        $routeCollection = $map->getRouteCollection($source, $target);

        if (false === $routeCollection->hasRoutes()) {
            return null;
        }

        $targetClassReflection = new \ReflectionClass($this->classCanonicalizer->getCanonicalFqcn($target));
        $targetPointValueAssignments = [];

        if (\is_string($target)) {
            $targetPointValueAssignments = $this->prepareTargetPointValueAssignments($source, $target, $routeCollection);

            $constructorArguments = [];
            foreach ($targetPointValueAssignments as $targetPointValueAssignment) {
                $targetPoint = $targetPointValueAssignment->getRoute()->getTargetPoint();
                
                if (!$targetPoint instanceof ParameterPoint || '__construct' !== $targetPoint->getMethodName()) {
                    continue;
                }

                if ($targetPointValueAssignment->hasTargetPointValueAssignmentDisabled()) {
                    continue;
                } elseif ($targetPointValueAssignment->hasTargetPointValueToAssign()) {
                    $constructorArgument = $targetPointValueAssignment->getTargetPointValueToAssign();
                } else {
                    $constructorArgument = $targetPointValueAssignment->getSourcePointValue();
                }

                $constructorArguments[$targetPoint->getPosition()] = $constructorArgument;
            }

            if (!empty($constructorArguments)) {
                $target = $targetClassReflection->newInstanceArgs($constructorArguments);
            } else {
                $target = $targetClassReflection->newInstance();
            }
        }

        $targetPointValueAssignments = $this->prepareTargetPointValueAssignments($source, $target, $routeCollection, $targetPointValueAssignments);

        $methodArguments = [];
        foreach ($targetPointValueAssignments as $targetPointValueAssignment) {
            $targetPoint = $targetPointValueAssignment->getRoute()->getTargetPoint();
            
            if (!$targetPoint instanceof ParameterPoint || '__construct' === $targetPoint->getMethodName()) {
                continue;
            }

            if ($targetPointValueAssignment->hasTargetPointValueAssignmentDisabled()) {
                continue;
            } elseif ($targetPointValueAssignment->hasTargetPointValueToAssign()) {
                $methodArgument = $targetPointValueAssignment->getTargetPointValueToAssign();
            } else {
                $methodArgument = $targetPointValueAssignment->getSourcePointValue();
            }

            $methodArguments[$targetPoint->getMethodName()][$targetPoint->getPosition()] = $methodArgument;
        }

        foreach ($methodArguments as $methodName => $arguments) {
            $targetClassReflection->getMethod($methodName)->invokeArgs($target, $arguments);
        }

        foreach ($targetPointValueAssignments as $targetPointValueAssignment) {
            $targetPoint = $targetPointValueAssignment->getRoute()->getTargetPoint();
            
            if (!$targetPoint instanceof PropertyPoint) {
                continue;
            }

            if ($targetPointValueAssignment->hasTargetPointValueAssignmentDisabled()) {
                continue;
            } elseif ($targetPointValueAssignment->hasTargetPointValueToAssign()) {
                $targetPoint->setValue($target, $targetPointValueAssignment->getTargetPointValueToAssign());
            } else {
                $targetPoint->setValue($target, $targetPointValueAssignment->getSourcePointValue());
            }
        }

        if (isset($targetPropertyPoints)) {
            foreach ($targetPropertyPoints as $propertyName => $targetPropertyPoint) {
                $targetPropertyPoint->setValue($target, $targetPropertyPointValues[$propertyName]);
            }
        }

        return $target;
    }

    /**
     * Prepares target point value assignments.
     * 
     * @param object $source
     * @param object|string $target
     * @param Opportus\ObjectMapper\Map\Route\RouteCollection $routeCollection
     * @param Opportus\ObjectMapperBundle\Event\TargetPointValueAssignmentEvent[] $targetPointValueAssignments
     * @return Opportus\ObjectMapperBundle\Event\TargetPointValueAssignmentEvent[]
     */
    private function prepareTargetPointValueAssignments(object $source, $target, RouteCollection $routeCollection, array $targetPointValueAssignmentsToMerge = []): array
    {
        $targetPointValueAssignments = [];
        foreach ($routeCollection as $route) {
            if (!\is_string($target) && $route->getTargetPoint() instanceof ParameterPoint && '__construct' === $route->getTargetPoint()->getMethodName()) {
                continue;
            }

            $targetPointValueAssignment = new TargetPointValueAssignmentEvent($route, $source, $target);

            foreach ($targetPointValueAssignmentsToMerge as $targetPointValueAssignmentToMerge) {
                if ($targetPointValueAssignmentToMerge->getRoute()->getFqn() === $targetPointValueAssignment->getRoute()->getFqn()) {
                    if ($targetPointValueAssignmentToMerge->hasTargetPointValueAssignmentDisabled()) {
                        $targetPointValueAssignment->disableTargetPointValueAssignment();
                    } elseif ($targetPointValueAssignmentToMerge->hasTargetPointValueToAssign()) {
                        $targetPointValueAssignment->setTargetPointValueToAssign($targetPointValueAssignmentToMerge->getTargetPointValueToAssign());
                    }

                    break;
                }
            }

            $eventName = \is_string($target) ? (ObjectMapperEvents::SET_NON_INSTANTIATED_TARGET_POINT_VALUE) : (ObjectMapperEvents::SET_INSTANTIATED_TARGET_POINT_VALUE);

            $this->eventDispatcher->dispatch($eventName, $targetPointValueAssignment);

            $targetPointValueAssignments[] = $targetPointValueAssignment;
        }

        return $targetPointValueAssignments;
    }
}
