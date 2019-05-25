<?php

namespace Opportus\ObjectMapperBundle\Event;

use Opportus\ObjectMapper\Exception\InvalidOperationException;
use Opportus\ObjectMapper\Map\Route\RouteInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * The target point value assignment event.
 *
 * @package Opportus\ObjectMapperBundle\Event
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class TargetPointValueAssignmentEvent extends Event implements TargetPointValueAssignmentEventInterface
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
     * @var object|string $target
     */
    private $target;

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
     * Constructs the target point value assignment event.
     *
     * @param Opportus\ObjectMapper\Map\Route\RouteInterface $route
     * @param object $source
     * @param object|string $target
     */
    public function __construct(RouteInterface $route, object $source, $target)
    {
        $this->route = $route;
        $this->source = $source;
        $this->target = $target;

        $this->hasTargetPointValueAssignmentDisabled = false;
        $this->hasTargetPointValueToAssign = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource(): object
    {
        return $this->source;
    }

    /**
     * {@inheritdoc}
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * {@inheritdoc}
     */
    public function disableTargetPointValueAssignment()
    {
        $this->hasTargetPointValueAssignmentDisabled = true;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTargetPointValueAssignmentDisabled(): bool
    {
        return $this->hasTargetPointValueAssignmentDisabled;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTargetPointValueToAssign(): bool
    {
        return $this->hasTargetPointValueToAssign;
    }

    /**
     * {@inheritdoc}
     */
    public function setTargetPointValueToAssign($value)
    {
        $this->targetPointValueToAssign = $value;
        $this->hasTargetPointValueToAssign = true;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getSourcePointValue()
    {
        return $this->route->getSourcePoint()->getValue($this->source);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoute(): RouteInterface
    {
        return $this->route;
    }

    /**
     * {@inheritdoc}
     */
    public function hasInstantiatedTarget(): bool
    {
        return \is_object($this->target);
    }
}
