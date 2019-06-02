<?php

namespace Opportus\ObjectMapperBundle;

use Opportus\ObjectMapperBundle\DependencyInjection\Compiler\MappingPredefinitionPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * The object mapper bundle.
 *
 * @package Opportus\ObjectMapperBundle
 * @author  Clément Cazaud <opportus@gmail.com>
 * @license https://github.com/opportus/ObjectMapperBundle/blob/master/LICENSE MIT
 */
final class OpportusObjectMapperBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new MappingPredefinitionPass());
    }
}
