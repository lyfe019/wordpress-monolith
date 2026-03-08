<?php
namespace Platinum\Core\Api\Middleware;

use Platinum\Core\Api\HttpRequest;
use Platinum\Core\Api\HttpResponse;
use Platinum\Core\Container\ServiceContainer;

final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(HttpRequest $request, callable $next)
    {
        // 1. ALLOW SMOKE TEST: Don't block the health check
        if ($request->path() === '/platinum/v1/ping') {
            $resolver = ServiceContainer::getInstance()->get('actor_resolver');
            $request->setActor($resolver->resolve());
            return $next($request);
        }

        // 2. Resolve Actor
        $resolver = ServiceContainer::getInstance()->get('actor_resolver');
        $actor = $resolver->resolve();
        $request->setActor($actor);

        // 3. Optional: Block everything else if not logged in
        // if (!$actor->isAuthenticated()) return HttpResponse::error('Unauthorized', 401);

        return $next($request);
    }
}