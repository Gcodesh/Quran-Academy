<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;
use PDO;
use PDOStatement;

class UserTest extends TestCase {
    
    public function testValidateReturnsTrueForValidData() {
        $user = new User();
        $data = [
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'password' => 'password123'
        ];
        
        $this->assertTrue($user->validate($data));
        $this->assertEmpty($user->getErrors());
    }

    public function testValidateReturnsFalseForInvalidEmail() {
        $user = new User();
        $data = [
            'email' => 'invalid-email',
            'full_name' => 'Test User'
        ];
        
        $this->assertFalse($user->validate($data));
        $this->assertContains("Invalid email address.", $user->getErrors());
    }

    /*
    public function testCreateCallsInsert() {
        $pdo = $this->createMock(PDO::class);
        $stmt = $this->createMock(PDOStatement::class);
        
        $pdo->method('prepare')->willReturn($stmt);
        $stmt->method('execute')->willReturn(true);
        $pdo->method('lastInsertId')->willReturn("1");

        $user = new User($pdo);
        $data = [
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'password' => 'password123',
            'role' => 'student'
        ];

        $result = $user->create($data);
        $this->assertEquals(1, $result);
    }
    */
}
