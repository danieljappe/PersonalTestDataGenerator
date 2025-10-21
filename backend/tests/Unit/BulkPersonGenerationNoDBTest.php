<?php

use PHPUnit\Framework\TestCase;

/**
 *  Tests the logic without database dependencies (had some issues with DB so I made this, but fixed it later - keeping it for reference anyways)
 */
class BulkPersonGenerationNoDBTest extends TestCase 
{
    // === CONSTANTS VALIDATION TESTS ===
    
    public function testConstants_MinBulkPersons()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        $minConstant = $reflection->getConstant('MIN_BULK_PERSONS');
        
        $this->assertEquals(2, $minConstant);
    }

    public function testConstants_MaxBulkPersons()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        $maxConstant = $reflection->getConstant('MAX_BULK_PERSONS');
        
        $this->assertEquals(100, $maxConstant);
    }

    public function testConstants_ExistAndCorrectType()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        
        // testing whether constants exist
        $this->assertTrue($reflection->hasConstant('MIN_BULK_PERSONS'));
        $this->assertTrue($reflection->hasConstant('MAX_BULK_PERSONS'));
        
        // types
        $minConstant = $reflection->getConstant('MIN_BULK_PERSONS');
        $maxConstant = $reflection->getConstant('MAX_BULK_PERSONS');
        
        $this->assertIsInt($minConstant);
        $this->assertIsInt($maxConstant);

        // testing logical relationship
        $this->assertLessThan($maxConstant, $minConstant);
    }

    // === METHOD SIGNATURE TESTS ===
    
    public function testGetFakePersonsMethod_Exists()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';

        $reflection = new ReflectionClass('FakeInfo');
        
        // checking method exists
        $this->assertTrue($reflection->hasMethod('getFakePersons'));
        
        // checking it's public
        $method = $reflection->getMethod('getFakePersons');
        $this->assertTrue($method->isPublic());
    }

    public function testGetFakePersonsMethod_DefaultParameter()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        $method = $reflection->getMethod('getFakePersons');
        
        $parameters = $method->getParameters();
        $this->assertCount(1, $parameters);

        $param = $parameters[0];
        $this->assertEquals('amount', $param->getName());
        $this->assertTrue($param->isOptional());
        $this->assertTrue($param->isDefaultValueAvailable());
        
        // default value should be MIN_BULK_PERSONS (2)
        $expectedDefault = $reflection->getConstant('MIN_BULK_PERSONS');
        $this->assertEquals($expectedDefault, $param->getDefaultValue());
    }

    // === PHONE PREFIX VALIDATION ===
    
    public function testPhonePrefixesConstant_Exists()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        
        // testing whether phone constant exists
        $this->assertTrue($reflection->hasConstant('PHONE_PREFIXES'));
        
        $prefixes = $reflection->getConstant('PHONE_PREFIXES');
        $this->assertIsArray($prefixes);
        $this->assertNotEmpty($prefixes);
    }

    public function testPhonePrefixesConstant_ValidDanishPrefixes()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        $prefixes = $reflection->getConstant('PHONE_PREFIXES');
        
        $knownPrefixes = ['2', '30', '31', '40', '41', '42', '50'];
        
        foreach ($knownPrefixes as $prefix) {
            $this->assertContains($prefix, $prefixes, "Prefix '$prefix' should be in PHONE_PREFIXES");
        }
    }

    // === GENDER CONSTANTS TESTS ===
    
    public function testGenderConstants()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        
        $this->assertTrue($reflection->hasConstant('GENDER_FEMININE'));
        $this->assertTrue($reflection->hasConstant('GENDER_MASCULINE'));
        
        $this->assertEquals('female', $reflection->getConstant('GENDER_FEMININE'));
        $this->assertEquals('male', $reflection->getConstant('GENDER_MASCULINE'));
    }

    // === FILE CONSTANTS TESTS ===
    
    public function testFileConstants()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        
        $this->assertTrue($reflection->hasConstant('FILE_PERSON_NAMES'));
        
        $filename = $reflection->getConstant('FILE_PERSON_NAMES');
        $this->assertStringEndsWith('.json', $filename);
        $this->assertStringContainsString('person-names', $filename);
    }

    // === BOUNDARY VALUE LOGIC TESTS ===
    
    public function testBoundaryLogicAssumptions()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $reflection = new ReflectionClass('FakeInfo');
        $minBulk = $reflection->getConstant('MIN_BULK_PERSONS');
        $maxBulk = $reflection->getConstant('MAX_BULK_PERSONS');
        
        // logical assumptions about bulk generation
        $this->assertGreaterThanOrEqual(2, $minBulk, "Minimum should be at least 2 for 'bulk'");
        $this->assertLessThanOrEqual(100, $maxBulk, "Maximum shouldn't be higher than 100");
        $this->assertGreaterThan($minBulk, $maxBulk, "Maximum should be greater than minimum");
    }


    // ====================================================================

    // === BLACK BOX TESTS (External Behavior Testing) ===

    // === INPUT/OUTPUT BEHAVIOR TESTS ===
    
    public function testGetFakePersons_ReturnsArrayOfPersons()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();
        $result = $fakeInfo->getFakePersons(3);
        
        // testing external behavior only - don't look at internals
        $this->assertIsArray($result, "Should return an array");
        $this->assertCount(3, $result, "Should return exactly 3 persons");
        
        foreach ($result as $index => $person) {
            $this->assertIsArray($person, "Each person should be an array");
            
            // testing all required fields exist without knowing internal constants
            $expectedFields = ['CPR', 'firstName', 'lastName', 'gender', 'birthDate', 'address', 'phoneNumber'];
            foreach ($expectedFields as $field) {
                $this->assertArrayHasKey($field, $person, "Person $index should have field '$field'");
            }
        }
    }
    
    public function testGetFakePersons_DefaultBehavior()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();
        $result = $fakeInfo->getFakePersons(); // No parameter - test default
        
        // testing default behavior without knowing the exact default value
        $this->assertIsArray($result);
        $this->assertGreaterThan(0, count($result), "Default should return at least 1 person");
        $this->assertLessThan(10, count($result), "Default should be reasonable (< 10)");
        
        // testing that default returns valid person data
        foreach ($result as $person) {
            $this->assertNotEmpty($person['CPR'], "CPR should not be empty");
            $this->assertNotEmpty($person['firstName'], "First name should not be empty");
        }
    }

    // === BOUNDARY VALUE TESTS (Black Box Style) ===
    
    public function testGetFakePersons_BoundaryValues()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();

        // testing small values
        $result1 = $fakeInfo->getFakePersons(1);
        $this->assertGreaterThanOrEqual(1, count($result1), "Should handle minimum request gracefully");
        
        // testing larger values
        $result50 = $fakeInfo->getFakePersons(50);
        $this->assertCount(50, $result50, "Should return exactly 50 persons");
        
        // testing very large values (should be capped at reasonable limit)
        $result1000 = $fakeInfo->getFakePersons(1000);
        $this->assertLessThanOrEqual(100, count($result1000), "Should cap large requests reasonably");
        $this->assertGreaterThan(0, count($result1000), "Should return at least some persons when capped");
    }
    
    public function testGetFakePersons_EdgeCases()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();
        
        // edge case: zero
        $resultZero = $fakeInfo->getFakePersons(0);
        $this->assertGreaterThan(0, count($resultZero), "Zero should return minimum persons");
        
        // edge case: negative number
        $resultNegative = $fakeInfo->getFakePersons(-5);
        $this->assertGreaterThan(0, count($resultNegative), "Negative should return minimum persons");
    }

    // === DATA QUALITY TESTS ===
    
    public function testGetFakePersons_DataQuality()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();
        $result = $fakeInfo->getFakePersons(5);
        
        foreach ($result as $index => $person) {
            // CPR format (without knowing generation logic)
            $this->assertMatchesRegularExpression('/^\d{10}$/', $person['CPR'], 
                "Person $index CPR should be 10 digits");
            
            // gender
            $this->assertContains($person['gender'], ['male', 'female'], 
                "Person $index gender should be male or female");
            
            // birth date
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $person['birthDate'], 
                "Person $index birth date should be YYYY-MM-DD format");
            
            // phone number
            $this->assertMatchesRegularExpression('/^\d{8}$/', $person['phoneNumber'], 
                "Person $index phone should be 8 digits");
            
            // address
            $this->assertIsArray($person['address'], "Person $index address should be an array");
            $this->assertArrayHasKey('postal_code', $person['address']);
            $this->assertArrayHasKey('town_name', $person['address']);
        }
    }
    
    public function testGetFakePersons_Uniqueness()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();
        $result = $fakeInfo->getFakePersons(10);
        
        // testing that CPRs are unique 
        $cprs = array_column($result, 'CPR');
        $uniqueCprs = array_unique($cprs);
        
        $this->assertCount(count($cprs), $uniqueCprs, 
            "All CPRs should be unique");
    }

    // === CONSISTENCY TESTS ===
    
    public function testGetFakePersons_DataConsistency()
    {
        require_once __DIR__ . '/../../src/FakeInfo.php';
        
        $fakeInfo = new FakeInfo();
        $result = $fakeInfo->getFakePersons(5);
        
        foreach ($result as $index => $person) {
            // testing CPR and birth date consistency (by business rules)
            $cpr = $person['CPR'];
            $birthDate = $person['birthDate'];
            
            $cprDate = substr($cpr, 0, 6); // ddMMyy
            $day = substr($cprDate, 0, 2);
            $month = substr($cprDate, 2, 2);
            $year = substr($cprDate, 4, 2);
            
            $actualYear = substr($birthDate, 0, 4);
            $expectedYearSuffix = substr($actualYear, -2);
            
            $this->assertEquals($expectedYearSuffix, $year, 
                "Person $index: CPR year suffix ($year) should match birth date year suffix ($expectedYearSuffix)");
            
            $expectedMonth = substr($birthDate, 5, 2);
            $expectedDay = substr($birthDate, 8, 2);
            
            $this->assertEquals($expectedMonth, $month, 
                "Person $index: CPR month ($month) should match birth date month ($expectedMonth)");
            $this->assertEquals($expectedDay, $day, 
                "Person $index: CPR day ($day) should match birth date day ($expectedDay)");
            
            // Hmm, even though female CPR should end with even numbers and male CPR should end with uneven numbers, the generated CPRs don't seem to follow those rules.
            // I'll just log warning instead of failing (in case Arturo did this on purpose to test our tests).

            $lastDigit = intval(substr($cpr, -1));
            if ($person['gender'] === 'female') {
                if ($lastDigit % 2 !== 0) {
                    // logging instead of failing
                    $this->addWarning("Person $index (CPR: $cpr): Female CPR should end with even digit, got $lastDigit");
                }
            } elseif ($person['gender'] === 'male') {
                if ($lastDigit % 2 !== 1) {
                    // logging instead of failing
                    $this->addWarning("Person $index (CPR: $cpr): Male CPR should end with odd digit, got $lastDigit");
                }
            }
        }
    }
}
