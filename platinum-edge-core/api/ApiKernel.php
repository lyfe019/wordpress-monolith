<?php
namespace Platinum\Core\Api;

use Platinum\Shared\Identity\ActorResolver;
use Throwable;
use DomainException;
use InvalidArgumentException;

final class ApiKernel
{
    public function __construct(
        private Router $router,
        private ActorResolver $identityResolver,
        private array $middleware = []
    ) {}

    public function handle(HttpRequest $request): array
    {
        try {
            return $this->sendRequestThroughPipeline($request);
        } catch (DomainException $e) {
            // Business rule violations (e.g., "Training is full")
            return HttpResponse::error($e->getMessage(), 403);
        } catch (InvalidArgumentException $e) {
            // Validation errors
            return HttpResponse::error($e->getMessage(), 422);
        } catch (Throwable $e) {
            // Unexpected system failures
            $this->logInternalError($e);
            
            $message = Environment::isDebug() 
                ? $e->getMessage() 
                : "A system error occurred. Please try again later.";
                
            return HttpResponse::error($message, 500);
        }
    }

    private function sendRequestThroughPipeline(HttpRequest $request): array
    {
        // Existing pipeline logic: resolve actor -> middleware -> router
        // ...
    }

    private function logInternalError(Throwable $e): void
    {
        error_log(sprintf(
            "[PLATINUM-CRITICAL] %s in %s on line %d. Trace: %s",
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        ));
    }
}