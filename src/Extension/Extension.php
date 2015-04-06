<?php

namespace PhpZone\PhpZone\Extension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface Extension
{
    public function load(ContainerBuilder $container);
}
