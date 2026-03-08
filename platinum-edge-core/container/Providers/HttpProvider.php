<?php
namespace Platinum\Core\Container\Providers;

use Platinum\Core\Api\HttpRequest;
use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Container\ServiceProviderInterface;

final class HttpProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        // Request-scoped: new instance per HTTP request
        $container->factory(HttpRequest::class, function () {
            return new HttpRequest();
        });
    }
}
