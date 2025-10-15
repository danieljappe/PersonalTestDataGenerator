<?php

namespace PersonalTestDataGenerator\Tests\Mocks;

require_once __DIR__ . '/MockDB.php';

/**
 * Mock Town class for testing without actual database connections
 */
class MockTown extends MockDB
{
    private static int $townCount = 598; // Mock town count

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns mock town data instead of querying the database
     * 
     * @return array ['postal_code' => value, 'town_name' => value]
     */
    public function getRandomTown(): array
    {
        // Return predefined mock town data
        $mockTowns = [
            ['postal_code' => '1000', 'town_name' => 'København K'],
            ['postal_code' => '2100', 'town_name' => 'København Ø'],
            ['postal_code' => '8000', 'town_name' => 'Aarhus C'],
            ['postal_code' => '5000', 'town_name' => 'Odense C'],
            ['postal_code' => '9000', 'town_name' => 'Aalborg'],
            ['postal_code' => '6700', 'town_name' => 'Esbjerg'],
            ['postal_code' => '3400', 'town_name' => 'Hillerød'],
            ['postal_code' => '4000', 'town_name' => 'Roskilde']
        ];

        return $mockTowns[array_rand($mockTowns)];
    }
}