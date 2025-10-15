<?php

namespace PersonalTestDataGenerator\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

/**
 * Base test case class that provides common functionality for all tests
 */
class TestCase extends PHPUnitTestCase
{
    /**
     * Set up the test environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Reset any global state if needed
        $_SERVER = [];
        $_GET = [];
        $_POST = [];
    }

    /**
     * Helper method to simulate HTTP request parameters
     */
    protected function simulateHttpRequest(string $method = 'GET', string $uri = '/', array $getParams = []): void
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['REQUEST_URI'] = $uri;
        $_GET = $getParams;
    }

    /**
     * Helper method to capture output from functions that echo directly
     */
    protected function captureOutput(callable $callback): string
    {
        ob_start();
        $callback();
        return ob_get_clean();
    }

    /**
     * Helper method to validate JSON structure
     */
    protected function assertValidJson(string $json): array
    {
        $decoded = json_decode($json, true);
        $this->assertNotNull($decoded, 'Response should be valid JSON');
        $this->assertEquals(JSON_ERROR_NONE, json_last_error(), 'JSON should be valid');
        return $decoded;
    }

    /**
     * Helper method to validate CPR format (DDMMYYXXXX)
     */
    protected function assertValidCpr(string $cpr): void
    {
        $this->assertMatchesRegularExpression('/^\d{10}$/', $cpr, 'CPR should be 10 digits');
        
        // Extract date parts
        $day = (int) substr($cpr, 0, 2);
        $month = (int) substr($cpr, 2, 2);
        $year = (int) substr($cpr, 4, 2);
        
        $this->assertGreaterThanOrEqual(1, $day, 'Day should be valid');
        $this->assertLessThanOrEqual(31, $day, 'Day should be valid');
        $this->assertGreaterThanOrEqual(1, $month, 'Month should be valid');
        $this->assertLessThanOrEqual(12, $month, 'Month should be valid');
    }

    /**
     * Helper method to validate phone number format
     */
    protected function assertValidPhoneNumber(string $phone): void
    {
        $this->assertMatchesRegularExpression('/^\d{8}$/', $phone, 'Phone number should be 8 digits');
    }

    /**
     * Helper method to validate gender
     */
    protected function assertValidGender(string $gender): void
    {
        $this->assertContains($gender, ['male', 'female'], 'Gender should be male or female');
    }

    /**
     * Helper method to validate date format (Y-m-d)
     */
    protected function assertValidDate(string $date): void
    {
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $date, 'Date should be in Y-m-d format');
        $timestamp = strtotime($date);
        $this->assertNotFalse($timestamp, 'Date should be valid');
        $this->assertEquals($date, date('Y-m-d', $timestamp), 'Date should be properly formatted');
    }

    /**
     * Helper method to validate address structure
     */
    protected function assertValidAddress(array $address): void
    {
        $requiredKeys = ['street', 'number', 'floor', 'door', 'postal_code', 'town_name'];
        
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $address, "Address should have $key");
            $this->assertNotEmpty($address[$key], "Address $key should not be empty");
        }
        
        // Validate postal code (4 digits)
        $this->assertMatchesRegularExpression('/^\d{4}$/', $address['postal_code'], 'Postal code should be 4 digits');
    }
}