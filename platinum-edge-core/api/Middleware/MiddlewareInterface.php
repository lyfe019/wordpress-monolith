<?php
namespace Platinum\Core\Api\Middleware;

use Platinum\Core\Api\HttpRequest;

interface MiddlewareInterface
{
    public function handle(HttpRequest $request, callable $next);
}
