<?php
namespace Platinum\Core\Container\Providers;

use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Container\ServiceProviderInterface;
use Platinum\Shared\Identity\ActorResolver; // Import the interface
use Platinum\Shared\Identity\WordPressActorResolver;

final class IdentityProvider implements ServiceProviderInterface
{
    public function register(ServiceContainer $container): void
    {
        // Use the Interface name as the key for better dependency injection
        $container->singleton(ActorResolver::class, function() {
            return new WordPressActorResolver();
        });
        
        // Alias for backward compatibility if needed
        $container->singleton('actor_resolver', function($c) {
            return $c->get(ActorResolver::class);
        });
    }
}