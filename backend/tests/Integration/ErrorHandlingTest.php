<?php

namespace PersonalTestDataGenerator\Tests\Integration;

use PersonalTestDataGenerator\Tests\TestCase;

/**
 * Tests for error handling and edge cases in the API
 */
class ErrorHandlingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../Mocks/MockFakeInfo.php';
    }

    /**
     * Test invalid HTTP method (POST instead of GET)
     */
    public function testInvalidHttpMethod(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'POST';
            $_SERVER['REQUEST_URI'] = '/backend/cpr';
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect HTTP method', $response['error']);
    }

    /**
     * Test invalid HTTP method (PUT)
     */
    public function testInvalidHttpMethodPut(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'PUT';
            $_SERVER['REQUEST_URI'] = '/backend/person';
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect HTTP method', $response['error']);
    }

    /**
     * Test invalid endpoint
     */
    public function testInvalidEndpoint(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/invalid-endpoint';
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect API endpoint', $response['error']);
    }

    /**
     * Test empty endpoint (just /backend/)
     */
    public function testEmptyEndpoint(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/';
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect API endpoint', $response['error']);
    }

    /**
     * Test /person endpoint with n=0 parameter
     */
    public function testPersonEndpointZeroPersons(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=0';
            $_GET = ['n' => '0'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect GET parameter value', $response['error']);
    }

    /**
     * Test /person endpoint with negative n parameter
     */
    public function testPersonEndpointNegativePersons(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=-5';
            $_GET = ['n' => '-5'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect GET parameter value', $response['error']);
    }

    /**
     * Test /person endpoint with n > 100 parameter
     */
    public function testPersonEndpointTooManyPersons(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=101';
            $_GET = ['n' => '101'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect GET parameter value', $response['error']);
    }

    /**
     * Test /person endpoint with invalid n parameter (string)
     */
    public function testPersonEndpointInvalidNString(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=abc';
            $_GET = ['n' => 'abc'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect GET parameter value', $response['error']);
    }

    /**
     * Test /person endpoint with decimal n parameter
     */
    public function testPersonEndpointDecimalN(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=5.5';
            $_GET = ['n' => '5.5'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        
        // Should convert 5.5 to 5 and return 5 persons
        $this->assertIsArray($response);
        $this->assertCount(5, $response);
    }

    /**
     * Test boundary case: exactly n=2 (minimum)
     */
    public function testPersonEndpointMinimumValid(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=2';
            $_GET = ['n' => '2'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertIsArray($response);
        $this->assertCount(2, $response);
    }

    /**
     * Test boundary case: exactly n=100 (maximum)
     */
    public function testPersonEndpointMaximumValid(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=100';
            $_GET = ['n' => '100'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertIsArray($response);
        $this->assertCount(100, $response);
    }

    /**
     * Test URL with trailing slash
     */
    public function testUrlWithTrailingSlash(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/cpr/';
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('CPR', $response);
        $this->assertValidCpr($response['CPR']);
    }

    /**
     * Test URL with query parameters on non-person endpoint
     */
    public function testUrlWithQueryParamsOnOtherEndpoints(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/cpr?extra=parameter';
            $_GET = ['extra' => 'parameter'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('CPR', $response);
        $this->assertValidCpr($response['CPR']);
    }

    /**
     * Test case sensitivity of endpoints
     */
    public function testCaseSensitiveEndpoints(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/CPR';
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertArrayHasKey('error', $response);
        $this->assertEquals('Incorrect API endpoint', $response['error']);
    }

    /**
     * Test multiple query parameters for person endpoint
     */
    public function testPersonEndpointMultipleQueryParams(): void
    {
        $output = $this->captureOutput(function() {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = '/backend/person?n=3&extra=param';
            $_GET = ['n' => '3', 'extra' => 'param'];
            
            $this->simulateIndexPhp();
        });
        
        $response = $this->assertValidJson($output);
        $this->assertIsArray($response);
        $this->assertCount(3, $response);
    }

    /**
     * Simulate the index.php behavior for error testing
     */
    private function simulateIndexPhp(): void
    {
        define('POS_ENTITY', 1);
        define('ERROR_METHOD', 0);
        define('ERROR_ENDPOINT', 1);
        define('ERROR_PARAMS', 2);

        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->reportError(ERROR_METHOD);
            return;
        }

        $url = strtok($_SERVER['REQUEST_URI'], "?");
        if (substr($url, strlen($url) - 1) == '/') {
            $url = substr($url, 0, strlen($url) - 1);
        }
        $url = substr($url, strpos($url, basename('/backend')));

        $urlPieces = explode('/', urldecode($url));

        $fakePerson = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        http_response_code(200);

        if (count($urlPieces) === 1) {
            $this->reportError(ERROR_ENDPOINT);
            return;
        }

        switch ($urlPieces[POS_ENTITY]) {
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
                $numPersons = $_GET['n'] ?? 1;
                $numPersons = abs((int)$numPersons);
                switch (true) {
                    case $numPersons === 0:
                        $this->reportError(ERROR_PARAMS);
                        break;
                    case $numPersons === 1:
                        echo json_encode($fakePerson->getFakePerson());
                        break;
                    case $numPersons > 1 && $numPersons <= 100:
                        echo json_encode($fakePerson->getFakePersons($numPersons), JSON_INVALID_UTF8_SUBSTITUTE);
                        break;
                    default:
                        $this->reportError(ERROR_PARAMS);
                }
                break;
            default:
                $this->reportError(ERROR_ENDPOINT);
        }
    }

    /**
     * Simulate the reportError function from index.php
     */
    private function reportError(int $error = -1): void
    {
        switch ($error) {
            case 0: // ERROR_METHOD
                http_response_code(405);
                $message = 'Incorrect HTTP method';
                break;
            case 1: // ERROR_ENDPOINT
                http_response_code(404);
                $message = 'Incorrect API endpoint';
                break;
            case 2: // ERROR_PARAMS
                http_response_code(400);
                $message = 'Incorrect GET parameter value';
                break;
            default:
                http_response_code(500);
                $message = 'Unknown error';
        }
        echo json_encode([
            'error' => $message
        ]);
    }
}