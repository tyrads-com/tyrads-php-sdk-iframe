<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\TyrAdsSdk;
use Tyrads\TyradsSdk\Configuration;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;

class Php84CompatibilityTest extends TestCase
{
    private $apiKey = 'test-api-key';
    private $apiSecret = 'test-api-secret';

    public function testPhpVersionCompatibility()
    {
        $this->assertGreaterThanOrEqual('5.5.0', PHP_VERSION, 'PHP 5.5+ is required');
    }

    public function testClassInstantiation()
    {
        $sdk = TyrAdsSdk::make($this->apiKey, $this->apiSecret);
        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);
    }

    public function testConfigurationClass()
    {
        $config = new Configuration($this->apiKey, $this->apiSecret);
        $this->assertInstanceOf(Configuration::class, $config);
        $this->assertEquals($this->apiKey, $config->getApiKey());
        $this->assertEquals($this->apiSecret, $config->getApiSecret());
    }

    public function testAuthenticationRequestClass()
    {
        $request = new AuthenticationRequest('test-user-123', 25, 1);
        
        $this->assertInstanceOf(AuthenticationRequest::class, $request);
        
        $parsedData = $request->getParsedData();
        $this->assertIsArray($parsedData);
        $this->assertEquals('test-user-123', $parsedData['publisherUserId']);
        $this->assertEquals(25, $parsedData['age']);
        $this->assertEquals(1, $parsedData['gender']);
    }

    public function testPhp84SpecificFeatures()
    {
        if (version_compare(PHP_VERSION, '8.4.0', '>=')) {
            $this->assertTrue(class_exists('Tyrads\TyradsSdk\TyrAdsSdk'), 'TyrAdsSdk class should be available in PHP 8.4+');
            
            $sdk = TyrAdsSdk::make($this->apiKey, $this->apiSecret);
            $this->assertIsObject($sdk, 'SDK instance should be properly created in PHP 8.4+');
        } else {
            $this->markTestSkipped('PHP 8.4+ specific tests skipped on PHP ' . PHP_VERSION);
        }
    }

    public function testArraySyntaxCompatibility()
    {
        $testArray = array(
            'key1' => 'value1',
            'key2' => array('nested' => 'value2'),
            'key3' => 123
        );
        
        $this->assertIsArray($testArray);
        $this->assertEquals('value1', $testArray['key1']);
        $this->assertArrayHasKey('nested', $testArray['key2']);
    }

    public function testStringConcatenation()
    {
        $baseUrl = 'https://api.tyrads.com';
        $version = 'v3.0';
        $endpoint = '/auth';
        
        $fullUrl = $baseUrl . '/' . $version . $endpoint;
        $this->assertEquals('https://api.tyrads.com/v3.0/auth', $fullUrl);
    }

    public function testErrorHandling()
    {
        try {
            $config = new Configuration('', '');
            $this->assertInstanceOf(Configuration::class, $config);
        } catch (\Exception $e) {
            unset($e);
            $this->fail('Should not throw exception for empty credentials during instantiation');
        }
    }

    public function testConstants()
    {
        $this->assertTrue(defined('PHP_VERSION'), 'PHP_VERSION constant should be defined');
        $this->assertIsString(PHP_VERSION, 'PHP_VERSION should be a string');
    }
}