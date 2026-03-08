<?php
namespace Platinum\Core\Api\Middleware;

use Platinum\Core\Api\HttpRequest;
use Platinum\Core\Events\EventBus;
use Platinum\Shared\Identity\Actor;

final class AuditMiddleware implements MiddlewareInterface
{
    public function handle(HttpRequest $request, callable $next)
    {
        /** @var Actor $actor */
        $actor = $request->actor();

        EventBus::dispatch('api.request.received', [
            'path'        => $request->path(),
            'method'      => $request->method(),
            'actor_type'  => $actor->type(),
            'actor_id'    => $actor->id(),
            'authenticated' => $actor->isAuthenticated(),
            'roles'       => $actor->roles(),
        ]);

        // Optional runtime log (very useful in dev)
        error_log(sprintf(
            '[AUDIT] %s %s actor=%s id=%s',
            $request->method(),
            $request->path(),
            $actor->type(),
            $actor->id() ?? 'null'
        ));

        return $next($request);
    }
}
