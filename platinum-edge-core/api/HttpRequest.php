<?php
namespace Platinum\Core\Api;

use Platinum\Shared\Identity\Actor;
use Platinum\Shared\Identity\AnonymousUser;

final class HttpRequest
{
    private ?Actor $actor = null;
    
    // Phase 5: Allow manual injection for Testing
    public function __construct(
        private string $method = '', 
        private string $path = '', 
        private array $body = []
    ) {
        // If empty, fallback to Global Server vars (WordPress mode)
        $this->method = $method ?: ($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->path = $path ?: $this->detectPath();
    }

    public static function fromWp(\WP_REST_Request $wpRequest): self
    {
        $request = new self(
            $wpRequest->get_method(),
            '/' . ltrim($wpRequest->get_route(), '/'),
            $wpRequest->get_json_params() ?? []
        );
        return $request;
    }

    private function detectPath(): string
    {
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        return '/' . trim(str_replace('/wp-json', '', $uri), '/');
    }

    public function method(): string { return strtoupper($this->method); }
    public function path(): string { return $this->path; }
    public function json(): array { return $this->body ?: $this->readRawInput(); }

    private function readRawInput(): array {
        $raw = file_get_contents('php://input');
        return $raw ? (json_decode($raw, true) ?: []) : [];
    }

    public function setActor(Actor $actor): void { $this->actor = $actor; }
    public function actor(): Actor { return $this->actor ?? new AnonymousUser(); }
}