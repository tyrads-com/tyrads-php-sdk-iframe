<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\HttpClient;
use Tyrads\TyradsSdk\Configuration;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class HttpClientTest extends TestCase
{
    private function createMockConfig()
    {
        $mockConfig = $this->createMock(Configuration::class);
        $mockConfig->method('getParsedApiUrl')->willReturn('https://api.tyrads.com/v3.0');
        $mockConfig->method('getApiKey')->willReturn('test_api_key');
        $mockConfig->method('getApiSecret')->willReturn('test_api_secret');
        $mockConfig->method('getSdkPlatform')->willReturn('Web');
        $mockConfig->method('getSdkVersion')->willReturn('1.0.0');
        $mockConfig->method('getLanguage')->willReturn('en');
        return $mockConfig;
    }

    public function testHttpClientCanBeInstantiated()
    {
        $mockConfig = $this->createMockConfig();
        $mockGuzzle = $this->createMock(ClientInterface::class);
        $httpClient = new HttpClient($mockConfig, $mockGuzzle);

        $this->assertInstanceOf(HttpClient::class, $httpClient);
    }

    public function testHttpClientPostJsonSendsRequestSuccessfully()
    {
        $mockConfig = $this->createMockConfig();
        $mockGuzzle = $this->createMock(ClientInterface::class);
        $mockResponse = new Response(200, [], '{"success": true, "token": "test_token"}');

        $mockGuzzle->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.tyrads.com/v3.0/auth',
                $this->callback(function ($options) {
                    return isset($options['headers']['X-API-Key']) &&
                        isset($options['headers']['X-API-Secret']) &&
                        isset($options['headers']['X-SDK-Platform']) &&
                        isset($options['headers']['X-SDK-Version']) &&
                        isset($options['query']['lang']) &&
                        isset($options['json']);
                })
            )
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockConfig, $mockGuzzle);
        $result = $httpClient->postJson('/auth', array('test' => 'data'));

        $this->assertTrue(is_array($result));
        $this->assertArrayHasKey('rawBody', $result);
        $this->assertArrayHasKey('header', $result);
        $this->assertArrayHasKey('json', $result);
        $this->assertEquals('{"success": true, "token": "test_token"}', $result['rawBody']);
        $this->assertTrue($result['json']['success']);
        $this->assertEquals('test_token', $result['json']['token']);
    }

    public function testHttpClientThrowsExceptionOnClientError()
    {
        $mockConfig = $this->createMockConfig();
        $mockGuzzle = $this->createMock(ClientInterface::class);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(400);

        $mockResponse = new Response(400, [], '{"message": "Bad Request"}');

        $mockGuzzle->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockConfig, $mockGuzzle);
        $httpClient->postJson('/auth', array('test' => 'data'));
    }

    public function testHttpClientThrowsExceptionOnServerError()
    {
        $mockConfig = $this->createMockConfig();
        $mockGuzzle = $this->createMock(ClientInterface::class);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(500);

        $mockResponse = new Response(500, [], '{"message": "Internal Server Error"}');

        $mockGuzzle->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockConfig, $mockGuzzle);
        $httpClient->postJson('/auth', array('test' => 'data'));
    }

    public function testHttpClientHandlesEmptyErrorMessage()
    {
        $mockConfig = $this->createMockConfig();
        $mockGuzzle = $this->createMock(ClientInterface::class);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown error');

        $mockResponse = new Response(400, [], '{}');

        $mockGuzzle->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockConfig, $mockGuzzle);
        $httpClient->postJson('/auth', array('test' => 'data'));
    }

    public function testHttpClientHandlesNullLanguage()
    {
        // Create a fresh mock specifically for this test
        $mockConfig = $this->createMock(Configuration::class);
        $mockConfig->method('getParsedApiUrl')->willReturn('https://api.tyrads.com/v3.0');
        $mockConfig->method('getApiKey')->willReturn('test_api_key');
        $mockConfig->method('getApiSecret')->willReturn('test_api_secret');
        $mockConfig->method('getSdkPlatform')->willReturn('Web');
        $mockConfig->method('getSdkVersion')->willReturn('1.0.0');
        $mockConfig->method('getLanguage')->willReturn(null);

        $mockGuzzle = $this->createMock(ClientInterface::class);
        $mockResponse = new Response(200, [], '{"success": true}');

        $mockGuzzle->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.tyrads.com/v3.0/auth',
                $this->callback(function ($options) {
                    // Should not include lang in query when language is null
                    return !isset($options['query']);
                })
            )
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockConfig, $mockGuzzle);
        $httpClient->postJson('/auth', array());
    }

    public function testHttpClientHandlesEmptyLanguage()
    {
        // Create a fresh mock specifically for this test
        $mockConfig = $this->createMock(Configuration::class);
        $mockConfig->method('getParsedApiUrl')->willReturn('https://api.tyrads.com/v3.0');
        $mockConfig->method('getApiKey')->willReturn('test_api_key');
        $mockConfig->method('getApiSecret')->willReturn('test_api_secret');
        $mockConfig->method('getSdkPlatform')->willReturn('Web');
        $mockConfig->method('getSdkVersion')->willReturn('1.0.0');
        $mockConfig->method('getLanguage')->willReturn('');

        $mockGuzzle = $this->createMock(ClientInterface::class);
        $mockResponse = new Response(200, [], '{"success": true}');

        $mockGuzzle->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                'https://api.tyrads.com/v3.0/auth',
                $this->callback(function ($options) {
                    // Should not include lang in query when language is empty
                    return !isset($options['query']);
                })
            )
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($mockConfig, $mockGuzzle);
        $httpClient->postJson('/auth', array());
    }
}
