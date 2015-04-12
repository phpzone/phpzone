<?php

/*
 * This file is originally part of the Symfony package,
 * but because of its absence in Symfony 2.3 it was copied here.
 */

namespace PhpZone\PhpZone\Integration\DependencyInjection;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SubscriberService implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
    }
}
