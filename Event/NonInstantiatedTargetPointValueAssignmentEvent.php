<?php

namespace Opportus\ObjectMapperBundle\Event;

use Opportus\ObjectMapper\Map\Route\RouteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The non instantiated target point value assignment event.
 *
 * @package Opportus\ObjectMapperBundle\Event
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class NonInstantiatedTargetPointValueAssignmentEvent extends Event
{
    use TargetPointValueAssignmentEventTrait;

    /**
     * @var string $targetFqcn
     */
    private $targetFqcn;

    /**
     * Constructs the non instantiated target point value assignment event.
     *
     * @param Opportus\ObjectMapper\Map\Route\RouteInterface $route
     * @param object $source
     * @param string $targetFqcn
     */
    public function __construct(RouteInterface $route, object $source, string $targetFqcn)
    {
        $this->route = $route;
        $this->source = $source;
        $this->targetFqcn = $targetFqcn;
        $this->hasTargetPointValueAssignmentDisabled = false;
        $this->hasTargetPointValueToAssign = false;
    }

    /**
     * Gets the target FQCN.
     * 
     * @return string
     */
    public function getTargetFqcn(): string
    {
        return $this->targetFqcn;
    }
}
