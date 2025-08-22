<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Contract\AuthenticationSign;

class AuthenticationSignTest extends TestCase
{
    public function testAuthenticationSignCanBeInstantiatedWithAllParameters()
    {
        $token = 'test_token_123';
        $userId = 'user123';
        $age = 25;
        $gender = 1;

        $authSign = new AuthenticationSign($token, $userId, $age, $gender);

        $this->assertInstanceOf(AuthenticationSign::class, $authSign);
    }

    public function testAuthenticationSignGettersReturnCorrectValues()
    {
        $token = 'test_token_456';
        $userId = 'user456';
        $age = 30;
        $gender = 2;

        $authSign = new AuthenticationSign($token, $userId, $age, $gender);

        $this->assertEquals($token, $authSign->getToken());
        $this->assertEquals($userId, $authSign->getPublisherUserId());
        $this->assertEquals($age, $authSign->getAge());
        $this->assertEquals($gender, $authSign->getGender());
    }

    public function testAuthenticationSignHandlesEmptyStringToken()
    {
        $authSign = new AuthenticationSign('', 'user123', 25, 1);

        $this->assertEquals('', $authSign->getToken());
        $this->assertEquals('user123', $authSign->getPublisherUserId());
    }

    public function testAuthenticationSignHandlesZeroAge()
    {
        $authSign = new AuthenticationSign('token123', 'user123', 0, 1);

        $this->assertEquals(0, $authSign->getAge());
    }

    public function testAuthenticationSignHandlesMaleGender()
    {
        $authSign = new AuthenticationSign('token123', 'user123', 25, 1);

        $this->assertEquals(1, $authSign->getGender());
    }

    public function testAuthenticationSignHandlesFemaleGender()
    {
        $authSign = new AuthenticationSign('token123', 'user123', 25, 2);

        $this->assertEquals(2, $authSign->getGender());
    }
}
