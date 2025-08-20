<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\TyrAdsSdk;
use Tyrads\TyradsSdk\Configuration;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;
use Tyrads\TyradsSdk\Contract\AuthenticationSign;

class TyrAdsSdkTest extends TestCase
{
    public function testTyrAdsSdkCanBeInstantiatedUsingMakeMethod()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        
        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);
    }

    public function testTyrAdsSdkCanBeInstantiatedWithConfigurationObject()
    {
        $config = new Configuration('test_api_key', 'test_api_secret');
        $sdk = new TyrAdsSdk($config);
        
        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);
    }

    public function testTyrAdsSdkCanGenerateIframeUrlWithTokenString()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test_token_123';
        
        $url = $sdk->iframeUrl($token);
        
        $this->assertStringContainsString('https://sdk.tyrads.com', $url);
        $this->assertStringContainsString('token=test_token_123', $url);
    }

    public function testTyrAdsSdkCanGenerateIframeUrlWithAuthenticationSignObject()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $authSign = new AuthenticationSign('test_token_123', 'user123', 25, 1);
        
        $url = $sdk->iframeUrl($authSign);
        
        $this->assertStringContainsString('https://sdk.tyrads.com', $url);
        $this->assertStringContainsString('token=test_token_123', $url);
    }

    public function testTyrAdsSdkCanGenerateIframeUrlWithDeeplinkParameter()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test_token_123';
        $deeplinkTo = 'surveys';
        
        $url = $sdk->iframeUrl($token, $deeplinkTo);
        
        $this->assertStringContainsString('https://sdk.tyrads.com', $url);
        $this->assertStringContainsString('token=test_token_123', $url);
        $this->assertStringContainsString('to=surveys', $url);
    }

    public function testTyrAdsSdkThrowsExceptionForInvalidIframeUrlParameter()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $sdk->iframeUrl(123); // Invalid parameter type
    }

    public function testTyrAdsSdkUrlEncodesParametersCorrectly()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test token with spaces';
        $deeplinkTo = 'path/with/special chars';
        
        $url = $sdk->iframeUrl($token, $deeplinkTo);
        
        // PHP's urlencode() converts spaces to + instead of %20
        $this->assertStringContainsString('token=test+token+with+spaces', $url);
        $this->assertStringContainsString('to=path%2Fwith%2Fspecial+chars', $url);
    }

    public function testTyrAdsSdkAuthenticationRequestIsProperlyValidated()
    {
        $authRequest = new AuthenticationRequest('user123', 25, 1);
        
        // Test that validation passes without throwing exception
        $authRequest->validate(); // This should not throw
        
        $data = $authRequest->getParsedData();
        $this->assertArrayHasKey('publisherUserId', $data);
        $this->assertArrayHasKey('age', $data);
        $this->assertArrayHasKey('gender', $data);
    }

    public function testTyrAdsSdkInvalidAuthenticationRequestThrowsValidationError()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $invalidRequest = new AuthenticationRequest('', 25, 1); // Empty user ID
        $invalidRequest->validate();
    }
}