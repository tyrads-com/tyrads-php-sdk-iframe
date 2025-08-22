<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Contract\AuthenticationSign;

class AuthenticationSignTest extends TestCase
{
    public function testAuthenticationSignCanBeInstantiatedWithRequiredParameters()
    {
        $token = 'test_token_123';
        $userId = 'user123';

        $authSign = new AuthenticationSign($token, $userId);

        $this->assertInstanceOf(AuthenticationSign::class, $authSign);
    }

    public function testAuthenticationSignGettersReturnCorrectValues()
    {
        $token = 'test_token_456';
        $userId = 'user456';

        $authSign = new AuthenticationSign($token, $userId);

        $this->assertEquals($token, $authSign->getToken());
        $this->assertEquals($userId, $authSign->getPublisherUserId());
    }

    public function testAuthenticationSignHandlesEmptyStringToken()
    {
        $authSign = new AuthenticationSign('', 'user123');

        $this->assertEquals('', $authSign->getToken());
        $this->assertEquals('user123', $authSign->getPublisherUserId());
    }

    public function testAuthenticationSignHandlesNullToken()
    {
        $authSign = new AuthenticationSign(null, 'user123');

        $this->assertNull($authSign->getToken());
        $this->assertEquals('user123', $authSign->getPublisherUserId());
    }
}
