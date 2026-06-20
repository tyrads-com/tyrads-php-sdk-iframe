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

        $this->assertNotFalse(strpos($url, 'https://v4.sdk.tyrads.com'));
        $this->assertNotFalse(strpos($url, 'token=test_token_123'));
    }

    public function testTyrAdsSdkCanGenerateIframeUrlWithAuthenticationSignObject()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $authSign = new AuthenticationSign('test_token_123', 'user123', 25, 1);

        $url = $sdk->iframeUrl($authSign);

        $this->assertNotFalse(strpos($url, 'https://v4.sdk.tyrads.com'));
        $this->assertNotFalse(strpos($url, 'token=test_token_123'));
    }

    public function testTyrAdsSdkCanGenerateIframeUrlWithDeeplinkParameter()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test_token_123';
        $deeplinkTo = 'surveys';

        $url = $sdk->iframeUrl($token, $deeplinkTo);

        $this->assertNotFalse(strpos($url, 'https://v4.sdk.tyrads.com'));
        $this->assertNotFalse(strpos($url, 'token=test_token_123'));
        $this->assertNotFalse(strpos($url, 'to=surveys'));
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
        $this->assertNotFalse(strpos($url, 'token=test+token+with+spaces'));
        $this->assertNotFalse(strpos($url, 'to=path%2Fwith%2Fspecial+chars'));
    }

    public function testTyrAdsSdkAuthenticationRequestIsProperlyValidated()
    {
        $authRequest = new AuthenticationRequest('user123', array('age' => 25, 'gender' => 1));

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

        $invalidRequest = new AuthenticationRequest(''); // Empty user ID
        $invalidRequest->validate();
    }

    public function testTyrAdsSdkCanGeneratePremiumWidgetUrlWithTokenString()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test_token_123';

        $url = $sdk->iframePremiumWidget($token);

        $this->assertNotFalse(strpos($url, 'https://v4.sdk.tyrads.com/widget'));
        $this->assertNotFalse(strpos($url, 'token=test_token_123'));
    }

    public function testTyrAdsSdkCanGeneratePremiumWidgetUrlWithAuthenticationSignObject()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $authSign = new AuthenticationSign('test_token_123', 'user123', 25, 1);

        $url = $sdk->iframePremiumWidget($authSign);

        $this->assertNotFalse(strpos($url, 'https://v4.sdk.tyrads.com/widget'));
        $this->assertNotFalse(strpos($url, 'token=test_token_123'));
    }

    public function testTyrAdsSdkCanGeneratePremiumWidgetUrlWithNameParameter()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test_token_123';
        $name = 'premium-widget';

        $url = $sdk->iframePremiumWidget($token, $name);

        $this->assertNotFalse(strpos($url, 'https://v4.sdk.tyrads.com/widget'));
        $this->assertNotFalse(strpos($url, 'token=test_token_123'));
        $this->assertNotFalse(strpos($url, 'name=premium-widget'));
    }

    public function testTyrAdsSdkThrowsExceptionForInvalidPremiumWidgetUrlParameter()
    {
        $this->expectException(InvalidArgumentException::class);

        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $sdk->iframePremiumWidget(123); // Invalid parameter type
    }

    public function testTyrAdsSdkMakeMethodUsesEnvironmentVariables()
    {
        // Test that make method can work without explicit parameters
        // by falling back to environment variables (mocked)
        $sdk = TyrAdsSdk::make();

        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);
    }

    public function testTyrAdsSdkMakeMethodWithCustomLanguage()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'es');

        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);
    }

    public function testTyrAdsSdkPremiumWidgetUrlEncodesParametersCorrectly()
    {
        $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
        $token = 'test token with spaces';
        $name = 'widget/with/special chars';

        $url = $sdk->iframePremiumWidget($token, $name);

        $this->assertNotFalse(strpos($url, 'token=test+token+with+spaces'));
        $this->assertNotFalse(strpos($url, 'name=widget%2Fwith%2Fspecial+chars'));
    }

    public function testTyrAdsSdkMakeMethodAcceptsCustomApiVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v4.1');

        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);

        $reflection = new \ReflectionClass($sdk);
        $configProp = $reflection->getProperty('config');
        $configProp->setAccessible(true);
        $config = $configProp->getValue($sdk);

        $this->assertEquals('v4.1', $config->getApiVersion());
        $this->assertEquals('https://api.tyrads.com/v4.1', $config->getParsedApiUrl());
    }

    public function testTyrAdsSdkMakeMethodDefaultsApiVersionWhenOmitted()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret');

        $reflection = new \ReflectionClass($sdk);
        $configProp = $reflection->getProperty('config');
        $configProp->setAccessible(true);
        $config = $configProp->getValue($sdk);

        $this->assertEquals('v4.0', $config->getApiVersion());
        $this->assertEquals('https://api.tyrads.com/v4.0', $config->getParsedApiUrl());
    }

    public function testTyrAdsSdkMakeMethodWithLanguageAndVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'es', 'v5.0');

        $this->assertInstanceOf(TyrAdsSdk::class, $sdk);

        $reflection = new \ReflectionClass($sdk);
        $configProp = $reflection->getProperty('config');
        $configProp->setAccessible(true);
        $config = $configProp->getValue($sdk);

        $this->assertEquals('es', $config->getLanguage());
        $this->assertEquals('v5.0', $config->getApiVersion());
    }

    public function testTyrAdsSdkAuthenticateUsesV4InitializeAuthEndpointByDefault()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret');

        $mockHttp = $this->createMock(\Tyrads\TyradsSdk\HttpClient::class);
        $mockHttp->expects($this->once())
            ->method('postJson')
            ->with('/initialize/auth', $this->anything())
            ->willReturn(array('json' => array('data' => array('token' => 'tok'))));

        $reflection = new \ReflectionClass($sdk);
        $httpProp = $reflection->getProperty('http');
        $httpProp->setAccessible(true);
        $httpProp->setValue($sdk, $mockHttp);

        $request = new \Tyrads\TyradsSdk\Contract\AuthenticationRequest('user123');
        $sign = $sdk->authenticate($request);

        $this->assertInstanceOf(\Tyrads\TyradsSdk\Contract\AuthenticationSign::class, $sign);
        $this->assertEquals('tok', $sign->getToken());
    }

    public function testTyrAdsSdkIframeUrlUsesLegacyHostForV3ApiVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v3.0');

        $url = $sdk->iframeUrl('test_token_123');

        $this->assertStringStartsWith('https://sdk.tyrads.com?token=', $url);
        // Make sure we did NOT accidentally pick the v3 host as a substring of v4.sdk.tyrads.com.
        $this->assertFalse(strpos($url, 'v4.sdk.tyrads.com'));
    }

    public function testTyrAdsSdkIframeUrlUsesV4HostForV4ApiVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v4.0');

        $url = $sdk->iframeUrl('test_token_123');

        $this->assertStringStartsWith('https://v4.sdk.tyrads.com?token=', $url);
    }

    public function testTyrAdsSdkIframeUrlUsesV5HostForV5ApiVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v5.0');

        $url = $sdk->iframeUrl('test_token_123');

        $this->assertStringStartsWith('https://v5.sdk.tyrads.com?token=', $url);
    }

    public function testTyrAdsSdkPremiumWidgetUrlUsesLegacyHostForV3ApiVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v3.0');

        $url = $sdk->iframePremiumWidget('test_token_123');

        $this->assertStringStartsWith('https://sdk.tyrads.com/widget?token=', $url);
        $this->assertFalse(strpos($url, 'v4.sdk.tyrads.com'));
    }

    public function testTyrAdsSdkPremiumWidgetUrlUsesV4HostForV4ApiVersion()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v4.0');

        $url = $sdk->iframePremiumWidget('test_token_123');

        $this->assertStringStartsWith('https://v4.sdk.tyrads.com/widget?token=', $url);
    }

    public function testTyrAdsSdkAuthenticateUsesLegacyAuthEndpointForV3()
    {
        $sdk = TyrAdsSdk::make('test_key', 'test_secret', 'en', 'v3.0');

        $mockHttp = $this->createMock(\Tyrads\TyradsSdk\HttpClient::class);
        $mockHttp->expects($this->once())
            ->method('postJson')
            ->with('/auth', $this->anything())
            ->willReturn(array('json' => array('data' => array('token' => 'tok'))));

        $reflection = new \ReflectionClass($sdk);
        $httpProp = $reflection->getProperty('http');
        $httpProp->setAccessible(true);
        $httpProp->setValue($sdk, $mockHttp);

        $request = new \Tyrads\TyradsSdk\Contract\AuthenticationRequest('user123');
        $sign = $sdk->authenticate($request);

        $this->assertInstanceOf(\Tyrads\TyradsSdk\Contract\AuthenticationSign::class, $sign);
    }
}
