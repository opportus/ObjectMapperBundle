<?php

namespace Opportus\ObjectMapperBundle\Event;

use Opportus\ObjectMapper\Map\Route\RouteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The instantiated target point value assignment event.
 *
 * @package Opportus\ObjectMapperBundle\Event
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
final class InstantiatedTargetPointValueAssignmentEvent extends Event
{
    use TargetPointValueAssignmentEventTrait;

    /**
     * @var object $target
     */
    private $target;

    /**
     * Constructs the instantiated target point value assignment event.
     *
     * @param Opportus\ObjectMapper\Map\Route\RouteInterface $route
     * @param object $source
     * @param object $target
     */
    public function __construct(RouteInterface $route, object $source, object $target)
    {
        $this->route = $route;
        $this->source = $source;
        $this->target = $target;
        $this->hasTargetPointValueAssignmentDisabled = false;
        $this->hasTargetPointValueToAssign = false;
    }

    /**
     * Gets the target.
     * 
     * @return object
     */
    public function getTarget(): object
    {
        return $this->target;
    }
}
