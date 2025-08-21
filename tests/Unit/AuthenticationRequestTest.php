<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;

class AuthenticationRequestTest extends TestCase
{
    public function testAuthenticationRequestCanBeInstantiatedWithRequiredParameters()
    {
        $request = new AuthenticationRequest('user123', 25, 1);

        $data = $request->getParsedData();
        $this->assertEquals('user123', $data['publisherUserId']);
        $this->assertEquals(25, $data['age']);
        $this->assertEquals(1, $data['gender']);
    }

    public function testAuthenticationRequestCanBeInstantiatedWithOptionalParameters()
    {
        $optionalParams = array(
            'email' => 'test@example.com',
            'phoneNumber' => '+1234567890',
            'sub1' => 'sub1_value',
            'userGroup' => 'vip'
        );

        $request = new AuthenticationRequest('user123', 25, 1, $optionalParams);

        $data = $request->getParsedData();
        $this->assertEquals('test@example.com', $data['email']);
        $this->assertEquals('+1234567890', $data['phoneNumber']);
        $this->assertEquals('sub1_value', $data['sub1']);
        $this->assertEquals('vip', $data['userGroup']);
    }

    public function testAuthenticationRequestValidatesRequiredParametersCorrectly()
    {
        $request = new AuthenticationRequest('user123', 25, 1);

        // Test that validation passes without throwing exception
        $request->validate(); // This should not throw

        $this->assertTrue(true); // Simple assertion to mark test as passed
    }

    public function testAuthenticationRequestThrowsExceptionForEmptyPublisherUserId()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('', 25, 1);
        $request->validate();
    }

    public function testAuthenticationRequestThrowsExceptionForInvalidAge()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('user123', -1, 1);
        $request->validate();
    }

    public function testAuthenticationRequestThrowsExceptionForInvalidGender()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('user123', 25, 3);
        $request->validate();
    }

    public function testAuthenticationRequestValidatesEmailFormatWhenProvided()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('user123', 25, 1, array('email' => 'invalid-email'));
        $request->validate();
    }

    public function testAuthenticationRequestValidatesPhoneNumberFormatWhenProvided()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('user123', 25, 1, array('phoneNumber' => 'invalid'));
        $request->validate();
    }

    public function testAuthenticationRequestAcceptsValidEmailFormat()
    {
        $request = new AuthenticationRequest('user123', 25, 1, array('email' => 'test@example.com'));

        // Test that validation passes without throwing exception
        $request->validate(); // This should not throw

        $this->assertTrue(true); // Simple assertion to mark test as passed
    }

    public function testAuthenticationRequestAcceptsValidPhoneNumberFormat()
    {
        $request = new AuthenticationRequest('user123', 25, 1, array('phoneNumber' => '+1-234-567-8900'));

        // Test that validation passes without throwing exception
        $request->validate(); // This should not throw

        $this->assertTrue(true); // Simple assertion to mark test as passed
    }

    public function testAuthenticationRequestExcludesEmptyOptionalFieldsFromParsedData()
    {
        $request = new AuthenticationRequest('user123', 25, 1, array('email' => '', 'sub1' => 'value'));

        $data = $request->getParsedData();
        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayHasKey('sub1', $data);
        $this->assertEquals('value', $data['sub1']);
    }

    public function testAuthenticationRequestHandlesMinimumValidAge()
    {
        $request = new AuthenticationRequest('user123', 1, 1);

        // Test that validation passes without throwing exception
        $request->validate();

        $data = $request->getParsedData();
        $this->assertEquals(1, $data['age']);
    }

    public function testAuthenticationRequestHandlesMaximumValidAge()
    {
        $request = new AuthenticationRequest('user123', 120, 1);

        // Test that validation passes without throwing exception  
        $request->validate();

        $data = $request->getParsedData();
        $this->assertEquals(120, $data['age']);
    }

    public function testAuthenticationRequestHandlesAllOptionalParameters()
    {
        $optionalParams = array(
            'email' => 'test@example.com',
            'phoneNumber' => '+1234567890',
            'sub1' => 'sub1_value',
            'sub2' => 'sub2_value',
            'sub3' => 'sub3_value',
            'sub4' => 'sub4_value',
            'sub5' => 'sub5_value',
            'userGroup' => 'vip'
        );

        $request = new AuthenticationRequest('user123', 25, 1, $optionalParams);

        $data = $request->getParsedData();
        foreach ($optionalParams as $key => $value) {
            $this->assertArrayHasKey($key, $data);
            $this->assertEquals($value, $data[$key]);
        }
    }

    public function testAuthenticationRequestValidatesNullPublisherUserId()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest(null, 25, 1);
        $request->validate();
    }

    public function testAuthenticationRequestAcceptsZeroAge()
    {
        $request = new AuthenticationRequest('user123', 0, 1);

        // Test that validation passes - age 0 is valid (>= 0)
        $request->validate();

        $data = $request->getParsedData();
        $this->assertEquals(0, $data['age']);
    }

    public function testAuthenticationRequestAcceptsValidEmailWithPlusSign()
    {
        $request = new AuthenticationRequest('user123', 25, 1, array('email' => 'test+tag@example.com'));

        // Test that validation passes without throwing exception
        $request->validate();

        $this->assertTrue(true);
    }

    public function testAuthenticationRequestAcceptsInternationalPhoneNumber()
    {
        $request = new AuthenticationRequest('user123', 25, 1, array('phoneNumber' => '+44-20-7946-0958'));

        // Test that validation passes without throwing exception
        $request->validate();

        $this->assertTrue(true);
    }

    public function testAuthenticationRequestHandlesWhitespaceInOptionalFields()
    {
        $request = new AuthenticationRequest('user123', 25, 1, array(
            'email' => ' test@example.com ',
            'sub1' => '  value  '
        ));

        $data = $request->getParsedData();
        $this->assertEquals(' test@example.com ', $data['email']);
        $this->assertEquals('  value  ', $data['sub1']);
    }
}
