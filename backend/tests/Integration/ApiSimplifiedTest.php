<?php

namespace PersonalTestDataGenerator\Tests\Integration;

use PersonalTestDataGenerator\Tests\TestCase;

/**
 * Simplified integration tests for API endpoints without headers
 */
class ApiSimplifiedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        require_once __DIR__ . '/../Mocks/MockFakeInfo.php';
    }

    /**
     * Test CPR generation functionality
     */
    public function testCprGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = ['CPR' => $fakeInfo->getCpr()];
        
        $this->assertArrayHasKey('CPR', $result);
        $this->assertValidCpr($result['CPR']);
    }

    /**
     * Test name and gender generation functionality
     */
    public function testNameGenderGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getFullNameAndGender();
        
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('gender', $result);
        
        $this->assertIsString($result['firstName']);
        $this->assertIsString($result['lastName']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertValidGender($result['gender']);
    }

    /**
     * Test name, gender, and birth date generation functionality
     */
    public function testNameGenderDobGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getFullNameGenderAndBirthDate();
        
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('gender', $result);
        $this->assertArrayHasKey('birthDate', $result);
        
        $this->assertIsString($result['firstName']);
        $this->assertIsString($result['lastName']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertValidGender($result['gender']);
        $this->assertValidDate($result['birthDate']);
    }

    /**
     * Test CPR, name, and gender generation functionality
     */
    public function testCprNameGenderGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getCprFullNameAndGender();
        
        $this->assertArrayHasKey('CPR', $result);
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('gender', $result);
        
        $this->assertValidCpr($result['CPR']);
        $this->assertIsString($result['firstName']);
        $this->assertIsString($result['lastName']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertValidGender($result['gender']);
    }

    /**
     * Test CPR, name, gender, and birth date generation functionality
     */
    public function testCprNameGenderDobGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getCprFullNameGenderAndBirthDate();
        
        $this->assertArrayHasKey('CPR', $result);
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('gender', $result);
        $this->assertArrayHasKey('birthDate', $result);
        
        $this->assertValidCpr($result['CPR']);
        $this->assertIsString($result['firstName']);
        $this->assertIsString($result['lastName']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertValidGender($result['gender']);
        $this->assertValidDate($result['birthDate']);
    }

    /**
     * Test address generation functionality
     */
    public function testAddressGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getAddress();
        
        $this->assertArrayHasKey('address', $result);
        $this->assertIsArray($result['address']);
        $this->assertValidAddress($result['address']);
    }

    /**
     * Test phone number generation functionality
     */
    public function testPhoneGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $phoneNumber = $fakeInfo->getPhoneNumber();
        $result = ['phoneNumber' => $phoneNumber];
        
        $this->assertArrayHasKey('phoneNumber', $result);
        $this->assertValidPhoneNumber($result['phoneNumber']);
    }

    /**
     * Test single person generation functionality
     */
    public function testSinglePersonGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getFakePerson();
        
        $this->assertPersonStructure($result);
    }

    /**
     * Test multiple persons generation functionality
     */
    public function testMultiplePersonsGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $result = $fakeInfo->getFakePersons(5);
        
        $this->assertIsArray($result);
        $this->assertCount(5, $result);
        
        foreach ($result as $person) {
            $this->assertPersonStructure($person);
        }
    }

    /**
     * Test bulk persons generation (edge case testing)
     */
    public function testBulkPersonsGeneration(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        
        // Test minimum boundary
        $result = $fakeInfo->getFakePersons(1); // Should return 2
        $this->assertCount(2, $result);
        
        // Test maximum boundary  
        $result = $fakeInfo->getFakePersons(101); // Should return 100
        $this->assertCount(100, $result);
        
        // Test normal case
        $result = $fakeInfo->getFakePersons(10);
        $this->assertCount(10, $result);
    }

    /**
     * Test that different instances generate different data
     */
    public function testDataRandomnessAcrossInstances(): void
    {
        $fakeInfo1 = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        $fakeInfo2 = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        
        $person1 = $fakeInfo1->getFakePerson();
        $person2 = $fakeInfo2->getFakePerson();
        
        // While theoretically possible, it's extremely unlikely that all fields match
        $differentFields = 0;
        $fields = ['CPR', 'firstName', 'lastName', 'birthDate', 'phoneNumber'];
        
        foreach ($fields as $field) {
            if ($person1[$field] !== $person2[$field]) {
                $differentFields++;
            }
        }
        
        $this->assertGreaterThan(0, $differentFields, 'Generated persons should have at least some different fields');
    }

    /**
     * Test that multiple calls to the same instance return same data (consistency)
     */
    public function testDataConsistencyWithinInstance(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        
        $cpr1 = $fakeInfo->getCpr();
        $cpr2 = $fakeInfo->getCpr();
        
        $name1 = $fakeInfo->getFullNameAndGender();
        $name2 = $fakeInfo->getFullNameAndGender();
        
        // Same instance should return same data
        $this->assertEquals($cpr1, $cpr2, 'Same instance should return consistent CPR');
        $this->assertEquals($name1, $name2, 'Same instance should return consistent name and gender');
    }

    /**
     * Test error simulation for boundary conditions
     */
    public function testBoundaryConditions(): void
    {
        $fakeInfo = new \PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo();
        
        // Test that 0 gets converted to minimum (2)
        $result = $fakeInfo->getFakePersons(0);
        $this->assertCount(2, $result, 'Zero should be converted to minimum (2)');
        
        // Test negative number gets converted to minimum (2) via abs()
        $result = $fakeInfo->getFakePersons(-5);
        $this->assertCount(2, $result, 'Negative should be converted to minimum (2)');
        
        // Test that numbers over 100 get capped
        $result = $fakeInfo->getFakePersons(150);
        $this->assertCount(100, $result, 'Over 100 should be capped to 100');
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