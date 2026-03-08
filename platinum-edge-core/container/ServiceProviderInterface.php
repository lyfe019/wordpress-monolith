<?php
namespace Platinum\Core\Container;

interface ServiceProviderInterface
{
    public function register(ServiceContainer $container): void;
}