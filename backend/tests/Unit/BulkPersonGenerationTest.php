<?php

use PHPUnit\Framework\TestCase;

/**
 * White Box Tests for Bulk Person Generation
 * Tests the internal implementation logic of getFakePersons() method
 */
class BulkPersonGenerationTest extends TestCase 
{
    private FakeInfo $fakeInfo;

    protected function setUp(): void
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        require_once __DIR__ . '/../../src/Town.php';
        require_once __DIR__ . '/../../src/DB.php';
        
        // Skip database-dependent tests if DB not available
        try {
            $this->fakeInfo = new FakeInfo();
        } catch (Exception $e) {
            $this->markTestSkipped('Database not available: ' . $e->getMessage());
        }
    }

    // === BOUNDARY CONDITION TESTS ===
    
    public function testGetFakePersons_BelowMinimum()
    {
        // below minimum boundary test
        $result = $this->fakeInfo->getFakePersons(1);
        
        // should default to minimum (2 persons)
        $this->assertCount(2, $result);
    }

    public function testGetFakePersons_AboveMaximum()
    {
        // above maximum boundary test
        $result = $this->fakeInfo->getFakePersons(150);
        
        // should cap at maximum (100 persons)
        $this->assertCount(100, $result);
    }

    public function testGetFakePersons_DefaultParameter()
    {
        // default parameter value should be (MIN_BULK_PERSONS = 2)
        $result = $this->fakeInfo->getFakePersons();
        
        $this->assertCount(2, $result);
    }

    // === LOOP LOGIC TESTS ===
    
    public function testGetFakePersons_LoopExecution()
    {
        // testing that the for loop executes the correct number of times
        $testCases = [2, 5, 10, 25, 50, 100];
        
        foreach ($testCases as $amount) {
            $result = $this->fakeInfo->getFakePersons($amount);
            $this->assertCount($amount, $result);
        }
    }

    public function testGetFakePersons_NewInstancePerIteration()
    {
        // each iteration creates a new FakeInfo instance
        $result = $this->fakeInfo->getFakePersons(3);
        
        // each person data should be different (new instance = different data)
        $this->assertNotEquals($result[0]['CPR'], $result[1]['CPR']);
        $this->assertNotEquals($result[1]['CPR'], $result[2]['CPR']);
        $this->assertNotEquals($result[0]['CPR'], $result[2]['CPR']);
    }

    // === CONSTANTS VALIDATION TESTS ===
    
    public function testConstants_MinBulkPersons()
    {
        $reflection = new ReflectionClass('FakeInfo');
        $minConstant = $reflection->getConstant('MIN_BULK_PERSONS');
        
        $this->assertEquals(2, $minConstant);
    }

    public function testConstants_MaxBulkPersons()
    {
        $reflection = new ReflectionClass('FakeInfo');
        $maxConstant = $reflection->getConstant('MAX_BULK_PERSONS');
        
        $this->assertEquals(100, $maxConstant);
    }

    // === DATA STRUCTURE TESTS ===
    
    public function testGetFakePersons_ArrayStructure()
    {
        $result = $this->fakeInfo->getFakePersons(3);
        
        // checking that result is indexed array
        $this->assertTrue(array_is_list($result));
        
        // checking that each element has correct structure
        foreach ($result as $person) {
            $this->assertArrayHasKey('CPR', $person);
            $this->assertArrayHasKey('firstName', $person);
            $this->assertArrayHasKey('lastName', $person);
            $this->assertArrayHasKey('gender', $person);
            $this->assertArrayHasKey('birthDate', $person);
            $this->assertArrayHasKey('address', $person);
            $this->assertArrayHasKey('phoneNumber', $person);
        }
    }

    // === EDGE CASE TESTS ===
    
    public function testGetFakePersons_NegativeInput()
    {
        // edge case: negative input
        $result = $this->fakeInfo->getFakePersons(-5);
        
        // should have a minimum
        $this->assertCount(2, $result);
    }

    public function testGetFakePersons_ZeroInput()
    {
        // edge case: zero input
        $result = $this->fakeInfo->getFakePersons(0);
        
        // should have a minimum
        $this->assertCount(2, $result);
    }

    // === INTERNAL LOGIC TESTS ===
    
    public function testGetFakePersons_CallsGetFakePerson()
    {
        // Test that getFakePersons calls getFakePerson for each iteration
        $result = $this->fakeInfo->getFakePersons(2);
        
        foreach ($result as $person) {
            $this->assertIsArray($person);
            $this->assertCount(7, $person); // 7 fields in getFakePerson()
        }
    }

}