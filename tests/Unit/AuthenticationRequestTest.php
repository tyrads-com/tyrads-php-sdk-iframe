<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;

class AuthenticationRequestTest extends TestCase
{
    public function testAuthenticationRequestCanBeInstantiatedWithRequiredParameters()
    {
        $request = new AuthenticationRequest('user123');

        $data = $request->getParsedData();
        $this->assertEquals('user123', $data['publisherUserId']);
        $this->assertArrayNotHasKey('age', $data);
        $this->assertArrayNotHasKey('gender', $data);
    }

    public function testAuthenticationRequestCanBeInstantiatedWithOptionalParameters()
    {
        $optionalParams = array(
            'age' => 25,
            'gender' => 1,
            'email' => 'test@example.com',
            'phoneNumber' => '+1234567890',
            'sub1' => 'sub1_value',
            'userGroup' => 'vip'
        );

        $request = new AuthenticationRequest('user123', $optionalParams);

        $data = $request->getParsedData();
        $this->assertEquals(25, $data['age']);
        $this->assertEquals(1, $data['gender']);
        $this->assertEquals('test@example.com', $data['email']);
        $this->assertEquals('+1234567890', $data['phoneNumber']);
        $this->assertEquals('sub1_value', $data['sub1']);
        $this->assertEquals('vip', $data['userGroup']);
    }

    public function testAuthenticationRequestValidatesRequiredParametersCorrectly()
    {
        $request = new AuthenticationRequest('user123');

        // Test that validation passes without throwing exception
        $request->validate(); // This should not throw

        $this->assertTrue(true); // Simple assertion to mark test as passed
    }

    public function testAuthenticationRequestThrowsExceptionForEmptyPublisherUserId()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('');
        $request->validate();
    }

    public function testAuthenticationRequestThrowsExceptionForInvalidAge()
    {
        $this->expectException(InvalidArgumentException::class);

        $optionalParams = array('age' => -1);
        $request = new AuthenticationRequest('user123', $optionalParams);
        $request->validate();
    }

    public function testAuthenticationRequestThrowsExceptionForInvalidGender()
    {
        $this->expectException(InvalidArgumentException::class);

        $optionalParams = array('gender' => 3);
        $request = new AuthenticationRequest('user123', $optionalParams);
        $request->validate();
    }

    public function testAuthenticationRequestValidatesEmailFormatWhenProvided()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('user123', array('email' => 'invalid-email'));
        $request->validate();
    }

    public function testAuthenticationRequestValidatesPhoneNumberFormatWhenProvided()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest('user123', array('phoneNumber' => 'invalid'));
        $request->validate();
    }

    public function testAuthenticationRequestAcceptsValidEmailFormat()
    {
        $request = new AuthenticationRequest('user123', array('email' => 'test@example.com'));

        // Test that validation passes without throwing exception
        $request->validate(); // This should not throw

        $this->assertTrue(true); // Simple assertion to mark test as passed
    }

    public function testAuthenticationRequestAcceptsValidPhoneNumberFormat()
    {
        $request = new AuthenticationRequest('user123', array('phoneNumber' => '+1-234-567-8900'));

        // Test that validation passes without throwing exception
        $request->validate(); // This should not throw

        $this->assertTrue(true); // Simple assertion to mark test as passed
    }

    public function testAuthenticationRequestExcludesEmptyOptionalFieldsFromParsedData()
    {
        $request = new AuthenticationRequest('user123', array('age' => 25, 'gender' => 1, 'email' => '', 'sub1' => 'value'));

        $data = $request->getParsedData();
        $this->assertArrayNotHasKey('email', $data);
        $this->assertArrayHasKey('age', $data);
        $this->assertArrayHasKey('gender', $data);
        $this->assertArrayHasKey('sub1', $data);
        $this->assertEquals('value', $data['sub1']);
    }

    public function testAuthenticationRequestHandlesMinimumValidAge()
    {
        $optionalParams = array('age' => 1);
        $request = new AuthenticationRequest('user123', $optionalParams);

        // Test that validation passes without throwing exception
        $request->validate();

        $data = $request->getParsedData();
        $this->assertEquals(1, $data['age']);
    }

    public function testAuthenticationRequestHandlesMaximumValidAge()
    {
        $optionalParams = array('age' => 120);
        $request = new AuthenticationRequest('user123', $optionalParams);

        // Test that validation passes without throwing exception  
        $request->validate();

        $data = $request->getParsedData();
        $this->assertEquals(120, $data['age']);
    }

    public function testAuthenticationRequestHandlesAllOptionalParameters()
    {
        $optionalParams = array(
            'age' => 25,
            'gender' => 1,
            'email' => 'test@example.com',
            'phoneNumber' => '+1234567890',
            'sub1' => 'sub1_value',
            'sub2' => 'sub2_value',
            'sub3' => 'sub3_value',
            'sub4' => 'sub4_value',
            'sub5' => 'sub5_value',
            'userGroup' => 'vip'
        );

        $request = new AuthenticationRequest('user123', $optionalParams);

        $data = $request->getParsedData();
        foreach ($optionalParams as $key => $value) {
            $this->assertArrayHasKey($key, $data);
            $this->assertEquals($value, $data[$key]);
        }
    }

    public function testAuthenticationRequestValidatesNullPublisherUserId()
    {
        $this->expectException(InvalidArgumentException::class);

        $request = new AuthenticationRequest(null);
        $request->validate();
    }

    public function testAuthenticationRequestAcceptsZeroAge()
    {
        $optionalParams = array('age' => 0);
        $request = new AuthenticationRequest('user123', $optionalParams);

        // Test that validation passes - age 0 is valid (>= 0)
        $request->validate();

        $data = $request->getParsedData();
        $this->assertEquals(0, $data['age']);
    }

    public function testAuthenticationRequestAcceptsValidEmailWithPlusSign()
    {
        $request = new AuthenticationRequest('user123', array('email' => 'test+tag@example.com'));

        // Test that validation passes without throwing exception
        $request->validate();

        $this->assertTrue(true);
    }

    public function testAuthenticationRequestAcceptsInternationalPhoneNumber()
    {
        $request = new AuthenticationRequest('user123', array('phoneNumber' => '+44-20-7946-0958'));

        // Test that validation passes without throwing exception
        $request->validate();

        $this->assertTrue(true);
    }

    public function testAuthenticationRequestHandlesWhitespaceInOptionalFields()
    {
        $request = new AuthenticationRequest('user123', array(
            'email' => ' test@example.com ',
            'sub1' => '  value  '
        ));

        $data = $request->getParsedData();
        $this->assertEquals(' test@example.com ', $data['email']);
        $this->assertEquals('  value  ', $data['sub1']);
    }

    public function testAuthenticationRequestAcceptsNullAgeAndGender()
    {
        $request = new AuthenticationRequest('user123');

        // Test that validation passes with null age and gender
        $request->validate();

        $data = $request->getParsedData();
        $this->assertArrayNotHasKey('age', $data);
        $this->assertArrayNotHasKey('gender', $data);
        $this->assertEquals('user123', $data['publisherUserId']);
    }

    public function testAuthenticationRequestAcceptsNullAgeWithValidGender()
    {
        $optionalParams = array('gender' => 1);
        $request = new AuthenticationRequest('user123', $optionalParams);

        // Test that validation passes
        $request->validate();

        $data = $request->getParsedData();
        $this->assertArrayNotHasKey('age', $data);
        $this->assertArrayHasKey('gender', $data);
        $this->assertEquals(1, $data['gender']);
    }

    public function testAuthenticationRequestAcceptsNullGenderWithValidAge()
    {
        $optionalParams = array('age' => 25);
        $request = new AuthenticationRequest('user123', $optionalParams);

        // Test that validation passes
        $request->validate();

        $data = $request->getParsedData();
        $this->assertArrayHasKey('age', $data);
        $this->assertArrayNotHasKey('gender', $data);
        $this->assertEquals(25, $data['age']);
    }

    public function testAuthenticationRequestSerializesUserGroupArrayToJsonString()
    {
        $userGroup = array('tier' => 'gold', 'segment' => 'premium', 'score' => 95);
        $request = new AuthenticationRequest('user123', array('userGroup' => $userGroup));

        $request->validate();
        $data = $request->getParsedData();

        $this->assertArrayHasKey('userGroup', $data);
        $this->assertTrue(is_string($data['userGroup']));
        $this->assertEquals(json_encode($userGroup), $data['userGroup']);
        $this->assertEquals($userGroup, json_decode($data['userGroup'], true));
    }

    public function testAuthenticationRequestSerializesNestedUserGroupArrayToJsonString()
    {
        $userGroup = array(
            'profile' => array('tier' => 'gold', 'flags' => array('vip', 'beta')),
            'region' => 'apac',
        );
        $request = new AuthenticationRequest('user123', array('userGroup' => $userGroup));

        $request->validate();
        $data = $request->getParsedData();

        $this->assertTrue(is_string($data['userGroup']));
        $this->assertEquals($userGroup, json_decode($data['userGroup'], true));
    }

    public function testAuthenticationRequestStillAcceptsUserGroupAsString()
    {
        $request = new AuthenticationRequest('user123', array('userGroup' => 'vip'));

        $request->validate();
        $data = $request->getParsedData();

        $this->assertSame('vip', $data['userGroup']);
    }

    public function testAuthenticationRequestStillAcceptsUserGroupAsAlreadyJsonEncodedString()
    {
        $preEncoded = '{"tier":"gold"}';
        $request = new AuthenticationRequest('user123', array('userGroup' => $preEncoded));

        $request->validate();
        $data = $request->getParsedData();

        // Pass-through: no double encoding
        $this->assertSame($preEncoded, $data['userGroup']);
    }

    public function testAuthenticationRequestRejectsUserGroupAsInteger()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new AuthenticationRequest('user123', array('userGroup' => 42));
        $request->validate();
    }

    public function testAuthenticationRequestRejectsUserGroupAsStdClass()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new AuthenticationRequest('user123', array('userGroup' => new \stdClass()));
        $request->validate();
    }

    public function testAuthenticationRequestExcludesEmptyUserGroupArrayFromParsedData()
    {
        $request = new AuthenticationRequest('user123', array('userGroup' => array()));
        $data = $request->getParsedData();

        $this->assertArrayNotHasKey('userGroup', $data);
    }

    public function testAuthenticationRequestExcludesEmptyUserGroupStringFromParsedData()
    {
        $request = new AuthenticationRequest('user123', array('userGroup' => ''));
        $data = $request->getParsedData();

        $this->assertArrayNotHasKey('userGroup', $data);
    }

    public function testAuthenticationRequestPersistsAllSubFields()
    {
        $params = array(
            'sub1' => 's1',
            'sub2' => 's2',
            'sub3' => 's3',
            'sub4' => 's4',
            'sub5' => 's5',
        );
        $request = new AuthenticationRequest('user123', $params);
        $request->validate();
        $data = $request->getParsedData();

        foreach ($params as $key => $expected) {
            $this->assertArrayHasKey($key, $data);
            $this->assertEquals($expected, $data[$key]);
        }
    }

    public function testAuthenticationRequestRejectsNonStringSubFields()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new AuthenticationRequest('user123', array('sub3' => array('not', 'allowed')));
        $request->validate();
    }

    public function testAuthenticationRequestPersistsAllMediaFields()
    {
        $params = array(
            'mediaSourceName'   => 'tiktok',
            'mediaSourceId'     => 'src_001',
            'mediaSubSourceId'  => 'sub_001',
            'mediaAdsetName'    => 'adset_a',
            'mediaAdsetId'      => 'adset_001',
            'mediaCreativeName' => 'creative_a',
            'mediaCreativeId'   => 'creative_001',
            'mediaCampaignName' => 'spring_launch',
        );
        $request = new AuthenticationRequest('user123', $params);
        $request->validate();
        $data = $request->getParsedData();

        foreach ($params as $key => $expected) {
            $this->assertArrayHasKey($key, $data);
            $this->assertEquals($expected, $data[$key]);
        }
    }

    public function testAuthenticationRequestRejectsNonStringMediaSourceId()
    {
        $this->expectException(InvalidArgumentException::class);
        $request = new AuthenticationRequest('user123', array('mediaSourceId' => 12345));
        $request->validate();
    }

    public function testAuthenticationRequestExcludesEmptyMediaFieldsFromParsedData()
    {
        $request = new AuthenticationRequest('user123', array(
            'mediaSourceName' => '',
            'mediaAdsetId'    => 'adset_001',
        ));
        $data = $request->getParsedData();

        $this->assertArrayNotHasKey('mediaSourceName', $data);
        $this->assertEquals('adset_001', $data['mediaAdsetId']);
    }

    public function testAuthenticationRequestPersistsIncentivizedTrue()
    {
        $request = new AuthenticationRequest('user123', array('incentivized' => true));
        $request->validate();
        $data = $request->getParsedData();

        $this->assertArrayHasKey('incentivized', $data);
        $this->assertTrue($data['incentivized']);
    }

    public function testAuthenticationRequestPersistsIncentivizedFalse()
    {
        $request = new AuthenticationRequest('user123', array('incentivized' => false));
        $request->validate();
        $data = $request->getParsedData();

        // false must be kept (not filtered out by the empty-string check)
        $this->assertArrayHasKey('incentivized', $data);
        $this->assertFalse($data['incentivized']);
    }

    public function testAuthenticationRequestIncentivizedIsCoercedToBoolean()
    {
        // setOptionalParams casts to bool, so validate() should pass
        $request = new AuthenticationRequest('user123', array('incentivized' => 1));
        $request->validate();
        $data = $request->getParsedData();

        $this->assertTrue($data['incentivized']);
    }
}
