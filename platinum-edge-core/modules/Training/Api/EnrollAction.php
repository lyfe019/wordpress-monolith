<?php
namespace Platinum\Modules\Training\Api;

use Platinum\Core\Api\HttpRequest;
use Platinum\Core\Api\HttpResponse;
use Platinum\Modules\Training\Application\Commands\EnrollTraining;
use Platinum\Modules\Training\Application\Handlers\EnrollHandler;

final class EnrollAction
{
    public function __construct(
        private EnrollHandler $handler
    ) {}

    public function handle(HttpRequest $request): array
    {
        // 1. Identify the Actor (Student) from the request context
        $actor = $request->actor();
        
        // 2. Extract input data
        $data = $request->json();
        $trainingId = $data['training_id'] ?? null;

        if (!$trainingId) {
            return HttpResponse::json(['error' => 'Training ID is required'], 422);
        }

        try {
            // 3. Create the Command (DTO)
            $command = new EnrollTraining($actor, $trainingId);

            // 4. Delegate to the Application Handler
            $result = $this->handler->handle($command);

            // 5. Return the formatted response
            return HttpResponse::json([
                'status'      => 'enrolled',
                'training_id' => $result->enrollment()->trainingId(),
                'message'     => 'Enrollment successful'
            ], 201);

        } catch (\DomainException $e) {
            // Business rule violations (e.g., "Training is full")
            return HttpResponse::json(['error' => $e->getMessage()], 403);
        } catch (\Throwable $e) {
            // System level errors
            return HttpResponse::json(['error' => 'Internal Server Error'], 500);
        }
    }
}