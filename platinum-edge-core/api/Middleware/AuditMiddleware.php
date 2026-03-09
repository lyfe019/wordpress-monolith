<?php
namespace Platinum\Core\Api\Middleware;

use Platinum\Core\Api\HttpRequest;

final class AuditMiddleware implements MiddlewareInterface
{
    public function handle(HttpRequest $request, callable $next)
    {
        $actor = $request->actor();
        
        // 1. Log the Intent (The Request)
        $this->logActivity(sprintf(
            "START: [%s] %s | Actor: %s (ID: %s) | IP: %s",
            $request->method(),
            $request->path(),
            $actor->type(),
            $actor->id() ?? 'Guest',
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ));

        $startTime = microtime(true);

        // 2. Pass to the next middleware/action
        $response = $next($request);

        // 3. Log the Outcome (The Response)
        $duration = round((microtime(true) - $startTime) * 1000, 2);
        $status = is_array($response) ? ($response['status_code'] ?? 200) : 200;

        $this->logActivity(sprintf(
            "END: [%s] Status: %s | Duration: %sms",
            $request->path(),
            $status,
            $duration
        ));

        return $response;
    }

    private function logActivity(string $message): void
    {
        // For now, we use error_log. In Phase 8.2, this can move to a DB Audit Table.
        error_log("[PLATINUM-AUDIT] " . $message);
    }
}