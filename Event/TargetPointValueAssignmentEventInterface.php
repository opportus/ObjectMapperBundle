<?php

namespace Opportus\ObjectMapperBundle\Event;

use Opportus\ObjectMapper\Map\Route\RouteInterface;

/**
 * The target point value assignment event interface.
 *
 * @package Opportus\ObjectMapperBundle\Event
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
interface TargetPointValueAssignmentEventInterface
{
    /**
     * Gets the source.
     *
     * @return object
     */
    public function getSource(): object;

    /**
     * Gets the target.
     *
     * @return string|object
     */
    public function getTarget();

    /**
     * Disables the target point value assignment.
     */
    public function disableTargetPointValueAssignment();
    
    /**
     * Checks whether the target point value assignment has been disabled.
     *
     * @return bool
     */
    public function hasTargetPointValueAssignmentDisabled(): bool;

    /**
     * Checks whether the event has a target point value to assign.
     *
     * @return bool
     */
    public function hasTargetPointValueToAssign(): bool;

    /**
     * Sets the target point value to assign.
     *
     * @param mixed $value
     */
    public function setTargetPointValueToAssign($value);

    /**
     * Gets the target point value to assign.
     *
     * @return mixed
     * @throws Opportus\ObjectMapper\Exception\InvalidOperationException When the target point value to assign has not been defined
     */
    public function getTargetPointValueToAssign();

    /**
     * Gets the source point value.
     *
     * @return mixed
     */
    public function getSourcePointValue();

    /**
     * Gets the route.
     *
     * @return Opportus\ObjectMapper\Map\Route\RouteInterface
     */
    public function getRoute(): RouteInterface;

    /**
     * Checks whether the target is instantiated.
     *
     * @return bool
     */
    public function hasInstantiatedTarget(): bool;
}
