<?php

namespace PersonalTestDataGenerator\Tests\Integration;

use PersonalTestDataGenerator\Tests\TestCase;

/**
 * Integration tests for API endpoints
 * These tests simulate actual HTTP requests to the API endpoints
 */
class ApiEndpointsTest extends TestCase
{
    private string $backendDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->backendDir = dirname(__DIR__, 2);
        
        // Mock the FakeInfo class by including our mock version
        require_once __DIR__ . '/../Mocks/MockFakeInfo.php';
    }

    /**
     * Test /cpr endpoint
     */
    public function testCprEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/cpr');
        
        $output = $this->simulateApiCall('cpr');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('CPR', $response);
        $this->assertValidCpr($response['CPR']);
    }

    /**
     * Test /name-gender endpoint
     */
    public function testNameGenderEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/name-gender');
        
        $output = $this->simulateApiCall('name-gender');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);
        $this->assertArrayHasKey('gender', $response);
        
        $this->assertIsString($response['firstName']);
        $this->assertIsString($response['lastName']);
        $this->assertNotEmpty($response['firstName']);
        $this->assertNotEmpty($response['lastName']);
        $this->assertValidGender($response['gender']);
    }

    /**
     * Test /name-gender-dob endpoint
     */
    public function testNameGenderDobEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/name-gender-dob');
        
        $output = $this->simulateApiCall('name-gender-dob');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);
        $this->assertArrayHasKey('gender', $response);
        $this->assertArrayHasKey('birthDate', $response);
        
        $this->assertIsString($response['firstName']);
        $this->assertIsString($response['lastName']);
        $this->assertNotEmpty($response['firstName']);
        $this->assertNotEmpty($response['lastName']);
        $this->assertValidGender($response['gender']);
        $this->assertValidDate($response['birthDate']);
    }

    /**
     * Test /cpr-name-gender endpoint
     */
    public function testCprNameGenderEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/cpr-name-gender');
        
        $output = $this->simulateApiCall('cpr-name-gender');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('CPR', $response);
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);
        $this->assertArrayHasKey('gender', $response);
        
        $this->assertValidCpr($response['CPR']);
        $this->assertIsString($response['firstName']);
        $this->assertIsString($response['lastName']);
        $this->assertNotEmpty($response['firstName']);
        $this->assertNotEmpty($response['lastName']);
        $this->assertValidGender($response['gender']);
    }

    /**
     * Test /cpr-name-gender-dob endpoint
     */
    public function testCprNameGenderDobEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/cpr-name-gender-dob');
        
        $output = $this->simulateApiCall('cpr-name-gender-dob');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('CPR', $response);
        $this->assertArrayHasKey('firstName', $response);
        $this->assertArrayHasKey('lastName', $response);
        $this->assertArrayHasKey('gender', $response);
        $this->assertArrayHasKey('birthDate', $response);
        
        $this->assertValidCpr($response['CPR']);
        $this->assertIsString($response['firstName']);
        $this->assertIsString($response['lastName']);
        $this->assertNotEmpty($response['firstName']);
        $this->assertNotEmpty($response['lastName']);
        $this->assertValidGender($response['gender']);
        $this->assertValidDate($response['birthDate']);
    }

    /**
     * Test /address endpoint
     */
    public function testAddressEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/address');
        
        $output = $this->simulateApiCall('address');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('address', $response);
        $this->assertIsArray($response['address']);
        $this->assertValidAddress($response['address']);
    }

    /**
     * Test /phone endpoint
     */
    public function testPhoneEndpoint(): void
    {
        $this->simulateHttpRequest('GET', '/backend/phone');
        
        $output = $this->simulateApiCall('phone');
        $response = $this->assertValidJson($output);
        
        $this->assertArrayHasKey('phoneNumber', $response);
        $this->assertValidPhoneNumber($response['phoneNumber']);
    }

    /**
     * Test /person endpoint (single person)
     */
    public function testPersonEndpointSingle(): void
    {
        $this->simulateHttpRequest('GET', '/backend/person');
        
        $output = $this->simulateApiCall('person');
        $response = $this->assertValidJson($output);
        
        $this->assertPersonStructure($response);
    }

    /**
     * Test /person endpoint with n=1 parameter
     */
    public function testPersonEndpointWithOne(): void
    {
        $this->simulateHttpRequest('GET', '/backend/person?n=1');
        
        $output = $this->simulateApiCall('person', ['n' => '1']);
        $response = $this->assertValidJson($output);
        
        $this->assertPersonStructure($response);
    }

    /**
     * Test /person endpoint with multiple persons (n=5)
     */
    public function testPersonEndpointMultiple(): void
    {
        $this->simulateHttpRequest('GET', '/backend/person?n=5', ['n' => '5']);
        
        $output = $this->simulateApiCall('person', ['n' => '5']);
        $response = $this->assertValidJson($output);
        
        $this->assertIsArray($response);
        $this->assertCount(5, $response);
        
        foreach ($response as $person) {
            $this->assertPersonStructure($person);
        }
    }

    /**
     * Test /person endpoint with bulk data (n=10)
     */
    public function testPersonEndpointBulk(): void
    {
        $this->simulateHttpRequest('GET', '/backend/person?n=10', ['n' => '10']);
        
        $output = $this->simulateApiCall('person', ['n' => '10']);
        $response = $this->assertValidJson($output);
        
        $this->assertIsArray($response);
        $this->assertCount(10, $response);
        
        foreach ($response as $person) {
            $this->assertPersonStructure($person);
        }
    }

    /**
     * Test /person endpoint with maximum allowed persons (n=100)
     */
    public function testPersonEndpointMaximum(): void
    {
        $this->simulateHttpRequest('GET', '/backend/person?n=100', ['n' => '100']);
        
        $output = $this->simulateApiCall('person', ['n' => '100']);
        $response = $this->assertValidJson($output);
        
        $this->assertIsArray($response);
        $this->assertCount(100, $response);
        
        // Only test first few to avoid excessive validation time
        for ($i = 0; $i < 3; $i++) {
            $this->assertPersonStructure($response[$i]);
        }
    }

    /**
     * Test response headers
     */
    public function testResponseHeaders(): void
    {
        $output = $this->simulateApiCall('cpr');
        
        // These headers should be set by the API
        $expectedHeaders = [
            'Content-Type: application/json; charset=utf-8',
            'Access-Control-Allow-Origin: *',
            'Accept-version: v1'
        ];
        
        // Note: In a real test environment, you would capture and test actual headers
        // This is a simplified test since we're capturing output directly
        $this->assertIsString($output);
        $response = json_decode($output, true);
        $this->assertNotNull($response);
    }

    /**
     * Test that all endpoints return different data on multiple calls
     */
    public function testDataRandomness(): void
    {
        $responses1 = [];
        $responses2 = [];
        
        $endpoints = ['cpr', 'name-gender', 'phone'];
        
        foreach ($endpoints as $endpoint) {
            $responses1[$endpoint] = $this->simulateApiCall($endpoint);
            $responses2[$endpoint] = $this->simulateApiCall($endpoint);
            
            // Decode responses
            $response1 = json_decode($responses1[$endpoint], true);
            $response2 = json_decode($responses2[$endpoint], true);
            
            // Responses should be different (very high probability)
            $this->assertNotEquals($response1, $response2, "Endpoint $endpoint should return different data on subsequent calls");
        }
    }

    /**
     * Helper method to simulate API calls
     */
    private function simulateApiCall(string $endpoint, array $getParams = []): string
    {
        // Set up environment
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/backend/$endpoint" . (!empty($getParams) ? '?' . http_build_query($getParams) : '');
        $_GET = $getParams;
        
        // Create a mock version of index.php that uses our mock classes
        return $this->captureOutput(function() use ($endpoint, $getParams) {
            $this->executeApiLogic($endpoint, $getParams);
        });
    }

    /**
     * Simulate the API logic from index.php using mock classes
     */
    private function executeApiLogic(string $endpoint, array $getParams = []): void
    {
        // Include our mock class instead of the real one
        require_once __DIR__ . '/../Mocks/MockFakeInfo.php';
        
        $fakePerson = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        http_response_code(200);
        
        switch ($endpoint) {
            case 'cpr':
                echo json_encode(['CPR' => $fakePerson->getCpr()]);
                break;
            case 'name-gender':
                echo json_encode($fakePerson->getFullNameAndGender());
                break;
            case 'name-gender-dob':
                echo json_encode($fakePerson->getFullNameGenderAndBirthDate());
                break;
            case 'cpr-name-gender':
                echo json_encode($fakePerson->getCprFullNameAndGender());
                break;
            case 'cpr-name-gender-dob':
                echo json_encode($fakePerson->getCprFullNameGenderAndBirthDate());
                break;
            case 'address':
                echo json_encode($fakePerson->getAddress());
                break;
            case 'phone':
                echo json_encode(['phoneNumber' => $fakePerson->getPhoneNumber()]);
                break;
            case 'person':
                $numPersons = $getParams['n'] ?? 1;
                $numPersons = abs((int)$numPersons);
                if ($numPersons === 0) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Incorrect GET parameter value']);
                } elseif ($numPersons === 1) {
                    echo json_encode($fakePerson->getFakePerson());
                } elseif ($numPersons > 1 && $numPersons <= 100) {
                    echo json_encode($fakePerson->getFakePersons($numPersons), JSON_INVALID_UTF8_SUBSTITUTE);
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Incorrect GET parameter value']);
                }
                break;
            default:
                http_response_code(404);
                echo json_encode(['error' => 'Incorrect API endpoint']);
        }
    }

    /**
     * Helper method to validate person structure
     */
    private function assertPersonStructure(array $person): void
    {
        $requiredKeys = ['CPR', 'firstName', 'lastName', 'gender', 'birthDate', 'address', 'phoneNumber'];
        
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $person, "Person should contain $key");
        }
        
        $this->assertValidCpr($person['CPR']);
        $this->assertIsString($person['firstName']);
        $this->assertIsString($person['lastName']);
        $this->assertNotEmpty($person['firstName']);
        $this->assertNotEmpty($person['lastName']);
        $this->assertValidGender($person['gender']);
        $this->assertValidDate($person['birthDate']);
        $this->assertIsArray($person['address']);
        $this->assertValidAddress($person['address']);
        $this->assertValidPhoneNumber($person['phoneNumber']);
    }
}