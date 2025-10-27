<?php

namespace PersonalTestDataGenerator\Tests\Mocks;

require_once __DIR__ . '/MockTown.php';

/**
 * Mock FakeInfo class that uses mocked dependencies
 */
class MockFakeInfo
{
    public const GENDER_FEMININE = 'female';
    public const GENDER_MASCULINE = 'male';
    private const FILE_PERSON_NAMES = 'data/person-names.json';
    private const PHONE_PREFIXES = [
        '2', '30', '31', '40', '41', '42', '50', '51', '52', '53', '60', '61', '71', '81', '91', '92', '93'
    ];
    private const MIN_BULK_PERSONS = 2;
    private const MAX_BULK_PERSONS = 100;

    private string $cpr;    
    private string $firstName;
    private string $lastName;
    private string $gender;
    private string $birthDate;
    private array $address = [];
    private string $phone;  

    public function __construct()
    {
        $this->setFullNameAndGender();
        $this->setBirthDate();
        $this->setCpr();
        $this->setAddress();
        $this->setPhone();
    }
    
    private function setCpr(): void
    {
        if (!isset($this->birthDate)) {
            $this->setBirthDate();        
        }
        if (!isset($this->firstName) || !isset($this->lastName) || (!isset($this->gender))){
            $this->setFullNameAndGender();
        }
        
        // The CPR must end in an even number for females, odd for males
        if ($this->gender === self::GENDER_FEMININE) {
            // For females, ensure final digit is even (0, 2, 4, 6, 8)
            $finalDigit = mt_rand(0, 4) * 2;
        } else {
            // For males, ensure final digit is odd (1, 3, 5, 7, 9)
            $finalDigit = mt_rand(0, 4) * 2 + 1;
        }
        
        $this->cpr = substr($this->birthDate, 8, 2) . 
            substr($this->birthDate, 5, 2) .
            substr($this->birthDate, 2, 2) .
            self::getRandomDigit() .
            self::getRandomDigit() .
            self::getRandomDigit() .
            $finalDigit;
    }

    private function setBirthDate(): void 
    {
        $year = mt_rand(1900, date('Y'));
        $month = mt_rand(1, 12);
        if (in_array($month, [1, 3, 5, 7, 8, 10, 12])) {
            $day = mt_rand(1, 31);
        } else if (in_array($month, [4, 6, 9, 11])) {
            $day = mt_rand(1, 30);
        } else {
            $day = mt_rand(1, 28);
        }
        $this->birthDate = date_format(date_create("$year-$month-$day"), 'Y-m-d');
    }

    private function setFullNameAndGender(): void
    {
        // Use mock data instead of reading from file
        $mockPersons = [
            ['firstName' => 'Lars', 'lastName' => 'Nielsen', 'gender' => 'male'],
            ['firstName' => 'Anna', 'lastName' => 'Hansen', 'gender' => 'female'],
            ['firstName' => 'Lionel', 'lastName' => 'Andersen', 'gender' => 'male'],
            ['firstName' => 'Mette', 'lastName' => 'Jensen', 'gender' => 'female'],
            ['firstName' => 'Mohammed', 'lastName' => 'Larsen', 'gender' => 'male'],
            ['firstName' => 'Lise', 'lastName' => 'Petersen', 'gender' => 'female']
        ];
        
        $person = $mockPersons[mt_rand(0, count($mockPersons) - 1)];
        
        $this->firstName = $person['firstName'];
        $this->lastName = $person['lastName'];
        $this->gender = $person['gender'];
    }

    private function setAddress(): void
    {
        $this->address['street'] = self::getRandomText(40);
        $this->address['number'] = (string) mt_rand(1, 999);
        
        if (mt_rand(1, 10) < 3) {
            $this->address['number'] .= strtoupper($this->getRandomText(1, false));
        }

        if (mt_rand(1, 10) < 4) {
            $this->address['floor'] = 'st';
        } else {
            $this->address['floor'] = mt_rand(1, 99);
        }

        $doorType = mt_rand(1, 20);
        if ($doorType < 8) {
            $this->address['door'] = 'th';
        } elseif ($doorType < 15) {
            $this->address['door'] = 'tv';
        } elseif ($doorType < 17) {
            $this->address['door'] = 'mf';
        } elseif ($doorType < 19) {
            $this->address['door'] = mt_rand(1, 50);
        } else {
            $lowerCaseLetters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p'];
            $this->address['door'] = $lowerCaseLetters[mt_rand(0, count($lowerCaseLetters) - 1)];
            if ($doorType === 20) {
                $this->address['door'] .= '-';
            }
            $this->address['door'] .= mt_rand(1, 999);
        }

        // Use mock town instead of database
        $town = new MockTown();
        $townData = $town->getRandomTown();
        $this->address['postal_code'] = $townData['postal_code'];
        $this->address['town_name'] = $townData['town_name'];        
        unset($town);
    }
    
    private static function getRandomText(int $length = 1, bool $includeDanishCharacters = true): string
    {
        $validCharacters = [
            ' ', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 
            'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 
            'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 
            'Y', 'Z'
        ];
        if ($includeDanishCharacters) {
            $validCharacters = array_merge($validCharacters, ['æ', 'ø', 'å', 'Æ', 'Ø', 'Å']);
        }

        $text = $validCharacters[mt_rand(1, count($validCharacters) - 1)];
        for ($index = 1; $index < $length; $index++) {
            $text .= $validCharacters[mt_rand(0, count($validCharacters) - 1)];
        }
        return $text;
    }

    private function setPhone(): void
    {
        $phone = self::PHONE_PREFIXES[mt_rand(0, count(self::PHONE_PREFIXES) - 1)];
        $prefixLength = strlen($phone);
        for ($index = 0; $index < (8 - $prefixLength); $index++) {
            $phone .= self::getRandomDigit();
        }

        $this->phone = $phone;
    }

    // Public methods (same as original FakeInfo)
    public function getCpr(): string 
    {
        return $this->cpr; 
    }
    
    public function getFullNameAndGender(): array 
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'gender' => $this->gender
        ];
    }

    public function getFullNameGenderAndBirthDate(): array 
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'gender' => $this->gender,
            'birthDate' => $this->birthDate
        ];
    }

    public function getCprFullNameAndGender(): array
    {
        return [
            'CPR' => $this->cpr,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'gender' => $this->gender
        ];
    }

    public function getCprFullNameGenderAndBirthDate(): array
    {
        return [
            'CPR' => $this->cpr,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'gender' => $this->gender,
            'birthDate' => $this->birthDate
        ];
    }

    public function getAddress(): array
    {
        return ['address' => $this->address];
    }

    public function getPhoneNumber(): string
    {
        return $this->phone;
    }

    public function getFakePerson(): array 
    {
        return [
            'CPR' => $this->cpr,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'gender' => $this->gender,
            'birthDate' => $this->birthDate,
            'address' => $this->address,
            'phoneNumber' => $this->phone
        ];    
    }

    public function getFakePersons(int $amount = self::MIN_BULK_PERSONS): array 
    {
        if ($amount < self::MIN_BULK_PERSONS) { $amount = self::MIN_BULK_PERSONS; }
        if ($amount > self::MAX_BULK_PERSONS) { $amount = self::MAX_BULK_PERSONS; }

        $bulkInfo = [];
        for ($index = 0; $index < $amount; $index++) {
            $fakeInfo = new MockFakeInfo();
            $bulkInfo[] = $fakeInfo->getFakePerson();
        }
        return $bulkInfo;
    }

    private static function getRandomDigit(): string 
    {
        return (string) mt_rand(0, 9);
    }
}