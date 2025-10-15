<?php

namespace PersonalTestDataGenerator\Tests\Mocks;

/**
 * Mock DB class for testing without actual database connections
 */
class MockDB
{
    protected object $pdo;

    public function __construct()
    {
        // Create a mock PDO object
        $this->pdo = $this->createMockPDO();
    }

    protected function createMockPDO(): object
    {
        // Return a mock object that simulates PDO behavior
        return new class {
            public function prepare(string $sql): object
            {
                return new class {
                    public function execute(): bool
                    {
                        return true;
                    }
                    
                    public function fetch(): array
                    {
                        // Return mock town data
                        return [
                            'postal_code' => '2100',
                            'town_name' => 'København Ø',
                            'total' => 598
                        ];
                    }
                };
            }
        };
    }

    public function __destruct()
    {
        unset($this->pdo);
    }
}