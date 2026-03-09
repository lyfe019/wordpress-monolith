<?php
namespace Platinum\Tests\Bdd\Context;

use Behat\Behat\Context\Context;
use Platinum\Core\Api\ApiKernel;
use Platinum\Core\Api\HttpRequest;
use Platinum\Core\Container\ServiceContainer;
use PHPUnit\Framework\Assert;

class TrainingContext implements Context
{
    private $response;

    /**
     * @Given I am a logged-in student
     */
    public function iAmALoggedInStudent()
    {
        // In BDD context, we simulate the ActorResolver returning a valid Student Actor
        // We'll use a TestingServiceProvider to inject a 'MockActor'
    }

    /**
     * @When I enroll in training :id
     */
    public function iEnrollInTraining($id)
    {
        $kernel = ServiceContainer::getInstance()->get('api_kernel');
        
        // Simulate the HTTP Request coming from the outside
        $request = new HttpRequest('POST', '/platinum/v1/trainings/enroll', [
            'training_id' => $id
        ]);

        $this->response = $kernel->handle($request);
    }

    /**
     * @Then the enrollment should be successful
     */
    public function theEnrollmentShouldBeSuccessful()
    {
        Assert::assertEquals(201, $this->response['status_code']);
        
        $body = json_decode($this->response['body'], true);
        Assert::assertTrue($body['success']);
    }
}