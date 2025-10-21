<?php

namespace PersonalTestDataGenerator\Tests;

use PHPUnit\Framework\TestCase;
use PersonalTestDataGenerator\FakeInfo;

class AddressTest extends TestCase
{
    private static $fakeInfo;
    private static $persons;

    public static function setUpBeforeClass(): void
    {
        require_once __DIR__ . '/../src/FakeInfo.php';
        self::$fakeInfo = new \FakeInfo();

        // Generate 50 random persons for testing
        self::$persons = self::$fakeInfo->getFakePersons(50);
    }

    public function testStreetIsNotEmpty(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $this->assertNotEmpty($address['street']);
    }

    public function testStreetLength(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $this->assertLessThanOrEqual(40, mb_strlen($address['street']));
    }

    public function testNumberIsNotEmpty(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $this->assertNotEmpty($address['number']);
    }

    public function testNumberStartsWithDigits(): void
{
    foreach (self::$persons as $person) {
        $number = $person['address']['number'];
        $this->assertMatchesRegularExpression('/^\d{1,3}/', $number, "Number '{$number}' must start with 1-3 digits");
    }
}

    public function testNumberOptionalUppercaseLetter(): void
    {
        foreach (self::$persons as $person) {
            $number = $person['address']['number'];
            $this->assertMatchesRegularExpression('/^\d{1,3}[A-Z]?$/', $number, "Number '{$number}' may optionally end with an uppercase letter");
        }
    }

    public function testNumberNumericPartRange(): void
    {
        foreach (self::$persons as $person) {
            $number = $person['address']['number'];
            preg_match('/^\d{1,3}/', $number, $matches);
            $numericPart = (int)$matches[0];
            $this->assertGreaterThanOrEqual(1, $numericPart, "Numeric part of '{$number}' is less than 1");
            $this->assertLessThanOrEqual(999, $numericPart, "Numeric part of '{$number}' is greater than 999");
        }
    }

    public function testNumberLength(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $this->assertLessThanOrEqual(4, mb_strlen($address['number']));
    }

    public function testNumberContainsDigit(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $this->assertMatchesRegularExpression('/\d/', $address['number'], 'Number must contain at least one digit');
    
    }

    // FLOOR (loop through all persons)
    public function testFloor(): void
    {
        foreach (self::$persons as $person) {
            $address = $person['address'];
            $floor = $address['floor'];
            $this->assertTrue(
                $floor === 'st' || (is_int($floor) && $floor >= 1 && $floor <= 99),
                "Floor '{$floor}' is not valid"
            );
        }
    }

    // DOOR (loop through all persons)
    public function testDoor(): void
    {
        foreach (self::$persons as $person) {
            $address = $person['address'];
            $door = $address['door'];

            if (in_array($door, ['th', 'tv', 'mf'])) {
                $this->assertTrue(true); // valid string format
            } elseif (is_numeric($door) && $door >= 1 && $door <= 50) {
                $this->assertTrue(true); // valid number
            } elseif (preg_match('/^[a-zæøå]\d{1,3}$/u', $door)) {
                $this->assertTrue(true); // letter + number
            } elseif (preg_match('/^[a-zæøå]-\d{1,3}$/u', $door)) {
                $this->assertTrue(true); // letter + - + number
            } else {
                $this->fail("Door value '{$door}' does not match any expected format");
            }
        }
    }

    // POSTAL CODE
    public function testPostalCodeFormat(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $postalCode = $address['postal_code'];
        $this->assertMatchesRegularExpression('/^\d{4}$/', $postalCode, "Postal code must be exactly 4 digits");
    }

    public function testPostalCodeLength(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $this->assertSame(4, mb_strlen($address['postal_code']));
    }

    // TOWN NAME
    public function testTownNameFormat(): void
    {
        $address = self::$fakeInfo->getAddress()['address'];
        $townName = $address['town_name'];
        $this->assertMatchesRegularExpression('/^[A-ZÆØÅ]/u', $townName, "Town name must start with an uppercase letter");
    }

    // PHONE
    public function testPhoneNumberPrefix(): void
    {
        $prefixes = [
            '2', '30', '31', '40', '41', '42', '50', '51', '52', '53', '60', '61', '71', '81', '91', '92', '93', '342',
            '344', '345', '346', '347', '348', '349', '356', '357', '359', '362', '365', '366', '389', '398', '431',
            '441', '462', '466', '468', '472', '474', '476', '478', '485', '486', '488', '489', '493', '494', '495',
            '496', '498', '499', '542', '543', '545', '551', '552', '556', '571', '572', '573', '574', '577', '579',
            '584', '586', '587', '589', '597', '598', '627', '629', '641', '649', '658', '662', '663', '664', '665',
            '667', '692', '693', '694', '697', '771', '772', '782', '783', '785', '786', '788', '789', '826', '827', '829'
        ];

        $phone = self::$fakeInfo->getPhoneNumber();
        $startsWithValidPrefix = false;
        foreach ($prefixes as $prefix) {
            if (str_starts_with($phone, $prefix)) {
                $startsWithValidPrefix = true;
                break;
            }
        }
        $this->assertTrue($startsWithValidPrefix, "Phone number must start with a valid prefix");
    }

    public function testPhoneNumberFormat(): void
    {
        $phone = self::$fakeInfo->getPhoneNumber();
        $this->assertMatchesRegularExpression('/^\d{8}$/', $phone, "Phone number must be exactly 8 digits");
    }
}
