<?php
namespace Platinum\Core;

use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Container\Providers\HttpProvider;
use Platinum\Core\Container\Providers\IdentityProvider;
use Platinum\Core\Events\EventDispatcher;
use Platinum\Core\Modules\ModuleLoader;
use Platinum\Core\Api\Router;
use Platinum\Core\Api\Route;
use Platinum\Core\Api\ApiKernel;
use Platinum\Core\Api\HttpResponse;
use Platinum\Core\Api\Middleware\AuditMiddleware;
use Platinum\Core\Api\Middleware\AuthMiddleware;
use Platinum\Shared\Identity\ActorResolver; // Add this import
use Platinum\Modules\Training\TrainingModule;
use Platinum\Modules\Training\Api\EnrollAction;
use Platinum\Modules\Training\Api\Testing\SeedTrainingAction;
use Platinum\Modules\Training\Application\Handlers\EnrollHandler;

final class Kernel
{
    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) return;

        $container = ServiceContainer::getInstance();

        // 1. Register Infrastructure Providers
        (new HttpProvider())->register($container);
        (new IdentityProvider())->register($container);

        // 2. Register Core Services
        $container->singleton('event_dispatcher', fn() => new EventDispatcher());
        $container->singleton('module_loader', fn() => new ModuleLoader());

        // 3. Register Domain Modules
        (new TrainingModule())->register();

        // 4. Register Routing
        $container->singleton('api_router', function($c) {
            $router = new Router();
            
            $router->add(new Route(
                'GET',
                '/platinum/v1/ping',
                fn() => HttpResponse::json(['status' => 'ok', 'handshake' => 'verified'])
            ));

            $router->add(new Route(
                'POST',
                '/platinum/v1/testing/seed-training',
                function($request) {
                    $action = new SeedTrainingAction();
                    return $action->handle($request);
                }
            ));

            $router->add(new Route(
                'POST',
                '/platinum/v1/trainings/enroll',
                function($request) use ($c) {
                    $action = new EnrollAction($c->get(EnrollHandler::class));
                    return $action->handle($request);
                }
            ));

            $router->add(new Route(
                'GET',
                '/platinum/v1/portal/my-trainings',
                function($request) {
                    return HttpResponse::json([
                        'trainings' => [['id' => 101, 'title' => 'PHP Basics']]
                    ], 200);
                }
            ));

            return $router;
        });

        // 5. Register ApiKernel (NOW INJECTING RESOLVER)
        $container->singleton('api_kernel', function($c) {
            return new ApiKernel(
                $c->get('api_router'),
                $c->get(ActorResolver::class), // Pass the resolved WordPressActorResolver
                [new AuditMiddleware(), new AuthMiddleware()]
            );
        });

        self::$booted = true;
    }

    public static function reset(): void
    {
        self::$booted = false;
        ServiceContainer::reset();
    }

    public static function instance(): ServiceContainer
    {
        return ServiceContainer::getInstance();
    }
}