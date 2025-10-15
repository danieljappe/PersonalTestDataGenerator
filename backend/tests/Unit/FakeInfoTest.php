<?php

namespace PersonalTestDataGenerator\Tests\Unit;

use PersonalTestDataGenerator\Tests\TestCase;
use PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo;

/**
 * Unit tests for FakeInfo class methods
 */
class FakeInfoTest extends TestCase
{
    private MockFakeInfo $fakeInfo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeInfo = new MockFakeInfo();
    }

    /**
     * Test getCpr() method
     */
    public function testGetCpr(): void
    {
        $cpr = $this->fakeInfo->getCpr();
        
        $this->assertIsString($cpr);
        $this->assertValidCpr($cpr);
    }

    /**
     * Test getFullNameAndGender() method
     */
    public function testGetFullNameAndGender(): void
    {
        $result = $this->fakeInfo->getFullNameAndGender();
        
        $this->assertIsArray($result);
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
     * Test getFullNameGenderAndBirthDate() method
     */
    public function testGetFullNameGenderAndBirthDate(): void
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        
        $this->assertIsArray($result);
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
     * Test getCprFullNameAndGender() method
     */
    public function testGetCprFullNameAndGender(): void
    {
        $result = $this->fakeInfo->getCprFullNameAndGender();
        
        $this->assertIsArray($result);
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
     * Test getCprFullNameGenderAndBirthDate() method
     */
    public function testGetCprFullNameGenderAndBirthDate(): void
    {
        $result = $this->fakeInfo->getCprFullNameGenderAndBirthDate();
        
        $this->assertIsArray($result);
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
     * Test getAddress() method
     */
    public function testGetAddress(): void
    {
        $result = $this->fakeInfo->getAddress();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('address', $result);
        $this->assertIsArray($result['address']);
        
        $this->assertValidAddress($result['address']);
    }

    /**
     * Test getPhoneNumber() method
     */
    public function testGetPhoneNumber(): void
    {
        $phoneNumber = $this->fakeInfo->getPhoneNumber();
        
        $this->assertIsString($phoneNumber);
        $this->assertValidPhoneNumber($phoneNumber);
    }

    /**
     * Test getFakePerson() method
     */
    public function testGetFakePerson(): void
    {
        $result = $this->fakeInfo->getFakePerson();
        
        $this->assertIsArray($result);
        
        // Check all required keys
        $requiredKeys = ['CPR', 'firstName', 'lastName', 'gender', 'birthDate', 'address', 'phoneNumber'];
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $result, "Result should contain $key");
        }
        
        // Validate each field
        $this->assertValidCpr($result['CPR']);
        $this->assertIsString($result['firstName']);
        $this->assertIsString($result['lastName']);
        $this->assertNotEmpty($result['firstName']);
        $this->assertNotEmpty($result['lastName']);
        $this->assertValidGender($result['gender']);
        $this->assertValidDate($result['birthDate']);
        $this->assertIsArray($result['address']);
        $this->assertValidAddress($result['address']);
        $this->assertValidPhoneNumber($result['phoneNumber']);
    }

    /**
     * Test getFakePersons() method with default parameter
     */
    public function testGetFakePersonsDefault(): void
    {
        $result = $this->fakeInfo->getFakePersons();
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result, 'Default should return 2 persons');
        
        foreach ($result as $person) {
            $this->assertIsArray($person);
            $this->testPersonStructure($person);
        }
    }

    /**
     * Test getFakePersons() method with specific amount
     */
    public function testGetFakePersonsWithAmount(): void
    {
        $amount = 5;
        $result = $this->fakeInfo->getFakePersons($amount);
        
        $this->assertIsArray($result);
        $this->assertCount($amount, $result, "Should return $amount persons");
        
        foreach ($result as $person) {
            $this->assertIsArray($person);
            $this->testPersonStructure($person);
        }
    }

    /**
     * Test getFakePersons() method with minimum boundary
     */
    public function testGetFakePersonsMinimumBoundary(): void
    {
        $result = $this->fakeInfo->getFakePersons(1); // Should be adjusted to 2
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result, 'Minimum should be enforced to 2 persons');
    }

    /**
     * Test getFakePersons() method with maximum boundary
     */
    public function testGetFakePersonsMaximumBoundary(): void
    {
        $result = $this->fakeInfo->getFakePersons(101); // Should be adjusted to 100
        
        $this->assertIsArray($result);
        $this->assertCount(100, $result, 'Maximum should be enforced to 100 persons');
    }

    /**
     * Test that CPR gender consistency is maintained
     */
    public function testCprGenderConsistency(): void
    {
        // Test multiple times to check consistency
        for ($i = 0; $i < 10; $i++) {
            $fakeInfo = new MockFakeInfo();
            $cpr = $fakeInfo->getCpr();
            $nameGender = $fakeInfo->getFullNameAndGender();
            
            $lastDigit = (int) substr($cpr, -1);
            $expectedGenderFromCpr = ($lastDigit % 2 === 0) ? 'female' : 'male';
            
            $this->assertEquals(
                $expectedGenderFromCpr,
                $nameGender['gender'],
                'CPR last digit should match gender (even=female, odd=male)'
            );
        }
    }

    /**
     * Test that generated data is different between instances
     */
    public function testDataUniqueness(): void
    {
        $fakeInfo1 = new MockFakeInfo();
        $fakeInfo2 = new MockFakeInfo();
        
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
     * Helper method to test person structure
     */
    private function testPersonStructure(array $person): void
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