<?php
namespace Platinum\Core\Api;

use Platinum\Core\Api\Middleware\MiddlewareInterface;
use Platinum\Shared\Identity\ActorResolver;

final class ApiKernel
{
    /**
     * @param Router $router
     * @param ActorResolver $identityResolver Resolves WordPress identity into an Actor
     * @param MiddlewareInterface[] $globalMiddleware
     */
    public function __construct(
        private Router $router,
        private ActorResolver $identityResolver,
        private array $globalMiddleware = []
    ) {}

    /**
     * Handles the incoming request through the identity resolver and middleware pipeline.
     * Changed return type from array to mixed to support HttpResponse objects.
     */
    public function handle(HttpRequest $request): mixed
    {
        // 1. Resolve and Inject Identity before any processing
        // This ensures every middleware and action knows who the user is.
        $request->setActor($this->identityResolver->resolve());

        // 2. Route Matching
        $route = $this->router->match($request);

        if (!$route) {
            return HttpResponse::error('Not Found', 404);
        }

        // 3. Pipeline Construction
        $middlewareStack = array_merge($this->globalMiddleware, $route->middleware);

        // The core handler is the final destination of the pipeline
        $coreHandler = function (HttpRequest $req) use ($route) {
            $handler = $route->handler;

            // Handle Controller class methods [Controller::class, 'method']
            if (is_array($handler) && is_string($handler[0])) {
                $instance = new $handler[0]();
                return $instance->{$handler[1]}($req);
            }

            // Handle Closures or Invokables
            return call_user_func($handler, $req);
        };

        // 4. Execute Middleware Pipeline (Inside-Out)
        $pipeline = array_reduce(
            array_reverse($middlewareStack),
            fn ($next, MiddlewareInterface $mw) => fn ($req) => $mw->handle($req, $next),
            $coreHandler
        );

        return $pipeline($request);
    }
}