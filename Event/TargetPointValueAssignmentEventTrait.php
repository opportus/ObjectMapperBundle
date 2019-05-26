<?php

namespace Opportus\ObjectMapperBundle\Event;

use Opportus\ObjectMapper\Map\Route\RouteInterface;
use Opportus\ObjectMapper\Exception\InvalidOperationException;

/**
 * The target point value assignment event trait.
 *
 * @package Opportus\ObjectMapperBundle\Event
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
trait TargetPointValueAssignmentEventTrait
{
    /**
     * @var Opportus\ObjectMapper\Map\Route\RouteInterface $route
     */
    private $route;

    /**
     * @var object $source
     */
    private $source;

    /**
     * @var mixed $targetPointValueToAssign
     */
    private $targetPointValueToAssign;

    /**
     * @var bool $hasTargetPointValueAssignmentDisabled
     */
    private $hasTargetPointValueAssignmentDisabled;

    /**
     * @var bool $hasTargetPointValueToAssign
     */
    private $hasTargetPointValueToAssign;

    /**
     * Sets the target point value to assign.
     *
     * @param mixed $value
     */
    public function setTargetPointValueToAssign($value)
    {
        $this->targetPointValueToAssign = $value;
        $this->hasTargetPointValueToAssign = true;
    }

    /**
     * Checks whether the event has a target point value to assign.
     *
     * @return bool
     */
    public function hasTargetPointValueToAssign(): bool
    {
        return $this->hasTargetPointValueToAssign;
    }

    /**
     * Gets the target point value to assign.
     *
     * @return mixed
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the target point value to assign has not been defined
     */
    public function getTargetPointValueToAssign()
    {
        if ($this->hasTargetPointValueToAssign) {
            return $this->targetPointValueToAssign;
        }

        throw new InvalidOperationException(\sprintf(
            'Cannot call %s because the target point value to assign has not been definied yet. You can check it first with %s.',
            __METHOD__,
            __CLASS__.'::hasTargetPointValueToAssign()'
        ));
    }

    /**
     * Disables the target point value assignment.
     */
    public function disableTargetPointValueAssignment()
    {
        $this->hasTargetPointValueAssignmentDisabled = true;
    }
    
    /**
     * Checks whether the target point value assignment has been disabled.
     *
     * @return bool
     */
    public function hasTargetPointValueAssignmentDisabled(): bool
    {
        return $this->hasTargetPointValueAssignmentDisabled;
    }

    /**
     * Gets the route.
     *
     * @return Opportus\ObjectMapper\Map\Route\RouteInterface
     */
    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    /**
     * Gets the source.
     *
     * @return object
     */
    public function getSource(): object
    {
        return $this->source;
    }

    /**
     * Gets the source point value.
     *
     * @return mixed
     */
    public function getSourcePointValue()
    {
        return $this->route->getSourcePoint()->getValue($this->source);
    }
}
