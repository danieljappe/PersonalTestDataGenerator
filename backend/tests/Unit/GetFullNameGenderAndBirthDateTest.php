<?php

use PHPUnit\Framework\TestCase;
use PersonalTestDataGenerator\Tests\Mocks\MockFakeInfo;

require_once __DIR__ . '/../Mocks/MockFakeInfo.php';
require_once __DIR__ . '/../../src/FakeInfo.php';

class GetFullNameGenderAndBirthDateTest extends TestCase 
{
    private MockFakeInfo $fakeInfo;
    private ReflectionClass $reflection;

    public function setUp(): void 
    {
        $this->fakeInfo = new MockFakeInfo();
        $this->reflection = new ReflectionClass('FakeInfo');
    }

    public function tearDown(): void 
    {
        unset($this->fakeInfo);
        unset($this->reflection);
    }

    // =================================================================
    // WHITE-BOX TESTS
    // =================================================================
    
    public function testMethod_Exists()
    {
        $this->assertTrue($this->reflection->hasMethod('getFullNameGenderAndBirthDate'));
        
        $method = $this->reflection->getMethod('getFullNameGenderAndBirthDate');
        $this->assertTrue($method->isPublic());
    }
    
    public function testMethod_NoParameters()
    {
        $method = $this->reflection->getMethod('getFullNameGenderAndBirthDate');
        $parameters = $method->getParameters();
        
        $this->assertCount(0, $parameters, "Method should have no parameters");
    }

    // ======================================================
    // BLACK-BOX TESTS
    // ======================================================
    
    public function testReturnStructure_IsValid()
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        
        // Test 1: Returns array
        $this->assertIsArray($result);
        
        // Test 2: Has exactly 4 keys
        $this->assertCount(4, $result);
        
        // Test 3: Contains all required keys
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('gender', $result);
        $this->assertArrayHasKey('birthDate', $result);
    }

    /**
     * @dataProvider provideFieldValidation
     */
    public function testFieldValidation($field, $checkType, $checkValue): void 
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        
        switch ($checkType) {
            case 'isString':
                $this->assertIsString($result[$field]);
                break;
            case 'notEmpty':
                $this->assertNotEmpty($result[$field]);
                break;
            case 'inArray':
                $this->assertContains($result[$field], $checkValue);
                break;
            case 'matchesRegex':
                $this->assertMatchesRegularExpression($checkValue, $result[$field]);
                break;
            case 'validDate':
                $this->assertNotFalse(strtotime($result[$field]));
                break;
        }
    }
    public static function provideFieldValidation(): array 
    {
        return [
            // firstName validation
            ['firstName', 'isString', null],
            ['firstName', 'notEmpty', null],
            
            // lastName validation
            ['lastName', 'isString', null],
            ['lastName', 'notEmpty', null],
            
            // gender validation
            ['gender', 'isString', null],
            ['gender', 'inArray', ['male', 'female']],
            
            // birthDate validation
            ['birthDate', 'isString', null],
            ['birthDate', 'matchesRegex', '/^\d{4}-\d{2}-\d{2}$/'],
            ['birthDate', 'validDate', null],
        ];
    }

    /**
     * @dataProvider provideBirthDateBoundaries
     */
    public function testBirthDate_Boundaries($component, $minValue, $maxValue): void 
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        
        switch ($component) {
            case 'year':
                $value = (int)substr($result['birthDate'], 0, 4);
                break;
            case 'month':
                $value = (int)substr($result['birthDate'], 5, 2);
                break;
            case 'day':
                $value = (int)substr($result['birthDate'], 8, 2);
                break;
        }
        
        $this->assertGreaterThanOrEqual($minValue, $value);
        $this->assertLessThanOrEqual($maxValue, $value);
    }
    public static function provideBirthDateBoundaries(): array 
    {
        $currentYear = (int)date('Y');
        
        return [
            ['year', 1900, $currentYear],   // Year boundaries: 1900 to current year
            ['month', 1, 12],                // Month boundaries: 1-12
            ['day', 1, 31],                  // Day boundaries: 1-31
        ];
    }
    
    public function testData_ConsistentAcrossMultipleCalls()
    {
        $result1 = $this->fakeInfo->getFullNameGenderAndBirthDate();
        $result2 = $this->fakeInfo->getFullNameGenderAndBirthDate();
        
        // Same instance should return same data
        $this->assertEquals($result1, $result2);
    }
    
    public function testData_DifferentInstancesProduceDifferentData()
    {
        $fakeInfo1 = new MockFakeInfo();
        $fakeInfo2 = new MockFakeInfo();
        $fakeInfo3 = new MockFakeInfo();
        
        $result1 = $fakeInfo1->getFullNameGenderAndBirthDate();
        $result2 = $fakeInfo2->getFullNameGenderAndBirthDate();
        $result3 = $fakeInfo3->getFullNameGenderAndBirthDate();
        
        // At least one should be different (statistically)
        $allSame = ($result1 === $result2 && $result2 === $result3);
        
        $this->assertFalse($allSame, "Different instances should produce different data");
    }

    public function testGender_MatchesClassConstants()
    {
        $validGenders = [
            $this->reflection->getConstant('GENDER_FEMININE'),  // 'female'
            $this->reflection->getConstant('GENDER_MASCULINE')  // 'male'
        ];
        
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        
        $this->assertContains($result['gender'], $validGenders);
    }
    
    public function testGender_BothGendersAppearOverMultipleSamples()
    {
        $genders = [];
        
        // Generate 30 samples to test statistical distribution
        for ($i = 0; $i < 30; $i++) {
            $fakeInfo = new MockFakeInfo();
            $result = $fakeInfo->getFullNameGenderAndBirthDate();
            $genders[] = $result['gender'];
        }
        
        // Both genders should appear (statistical test)
        $this->assertContains('male', $genders);
        $this->assertContains('female', $genders);
    }

    // ======================================================
    // INTEGRATION TESTS
    // ======================================================
    
    /**
     * @dataProvider provideMethodIntegration
     */
    public function testIntegration_WithOtherMethods($otherMethod, $fieldsToCheck): void 
    {
        $result1 = $this->fakeInfo->getFullNameGenderAndBirthDate();
        $result2 = $this->fakeInfo->$otherMethod();
        
        foreach ($fieldsToCheck as $field) {
            $this->assertEquals($result2[$field], $result1[$field], 
                "Field '$field' should match between methods");
        }
    }
    public static function provideMethodIntegration(): array 
    {
        return [
            // Test with getFullNameAndGender()
            ['getFullNameAndGender', ['firstName', 'lastName', 'gender']],
            
            // Test with getCprFullNameGenderAndBirthDate()
            ['getCprFullNameGenderAndBirthDate', ['firstName', 'lastName', 'gender', 'birthDate']],
        ];
    }
    
    public function testIntegration_CprContainsBirthDate()
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        $cpr = $this->fakeInfo->getCpr();
        
        // CPR format: DDMMYY-XXXX (first 6 digits = birth date)
        $day = substr($result['birthDate'], 8, 2);
        $month = substr($result['birthDate'], 5, 2);
        $year = substr($result['birthDate'], 2, 2);
        
        $this->assertEquals($day, substr($cpr, 0, 2), "CPR should contain day");
        $this->assertEquals($month, substr($cpr, 2, 2), "CPR should contain month");
        $this->assertEquals($year, substr($cpr, 4, 2), "CPR should contain year");
    }
    
    public function testIntegration_CprMatchesGenderRule()
    {
        $result = $this->fakeInfo->getFullNameGenderAndBirthDate();
        $cpr = $this->fakeInfo->getCpr();
        
        // Danish CPR rule: Last digit even=female, odd=male
        $lastDigit = (int)substr($cpr, -1);
        
        if ($result['gender'] === 'female') {
            $this->assertEquals(0, $lastDigit % 2, "Female CPR should end with even digit");
        } else {
            $this->assertEquals(1, $lastDigit % 2, "Male CPR should end with odd digit");
        }
    }
}

