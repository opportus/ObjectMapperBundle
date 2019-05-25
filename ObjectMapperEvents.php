<?php

namespace Opportus\ObjectMapperBundle;

/**
 * The object mapper events.
 *
 * @package Opportus\ObjectMapperBundle
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/object-mapper/blob/master/LICENSE MIT
 */
class ObjectMapperEvents
{
    const SET_NON_INSTANTIATED_TARGET_POINT_VALUE = 'opportus_object_mapper.set_non_instantiated_target_point_value';
    const SET_INSTANTIATED_TARGET_POINT_VALUE = 'opportus_object_mapper.set_instantiated_target_point_value';
}
