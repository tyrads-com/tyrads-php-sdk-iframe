<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\HttpClient;
use Tyrads\TyradsSdk\Configuration;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Psr7\Response;

class HttpClientTest extends TestCase
{
    private $mockConfig;
    private $mockGuzzle;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockConfig = $this->createMock(Configuration::class);
        $this->mockConfig->method('getParsedApiUrl')->willReturn('https://api.tyrads.com/v3.0');
        $this->mockConfig->method('getApiKey')->willReturn('test_api_key');
        $this->mockConfig->method('getApiSecret')->willReturn('test_api_secret');
        $this->mockConfig->method('getSdkPlatform')->willReturn('Web');
        $this->mockConfig->method('getSdkVersion')->willReturn('1.0.0');
        $this->mockConfig->method('getLanguage')->willReturn('en');

        $this->mockGuzzle = $this->createMock(ClientInterface::class);
    }

    public function testHttpClientCanBeInstantiated()
    {
        $httpClient = new HttpClient($this->mockConfig, $this->mockGuzzle);

        $this->assertInstanceOf(HttpClient::class, $httpClient);
    }

    public function testHttpClientPostJsonSendsRequestSuccessfully()
    {
        $mockResponse = new Response(200, [], '{"success": true, "token": "test_token"}');

        $this->mockGuzzle->expects($this->once())
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

        $httpClient = new HttpClient($this->mockConfig, $this->mockGuzzle);
        $result = $httpClient->postJson('/auth', array('test' => 'data'));

        $this->assertIsArray($result);
        $this->assertArrayHasKey('rawBody', $result);
        $this->assertArrayHasKey('header', $result);
        $this->assertArrayHasKey('json', $result);
        $this->assertEquals('{"success": true, "token": "test_token"}', $result['rawBody']);
        $this->assertTrue($result['json']['success']);
        $this->assertEquals('test_token', $result['json']['token']);
    }

    public function testHttpClientThrowsExceptionOnClientError()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(400);

        $mockResponse = new Response(400, [], '{"message": "Bad Request"}');

        $this->mockGuzzle->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($this->mockConfig, $this->mockGuzzle);
        $httpClient->postJson('/auth', array('test' => 'data'));
    }

    public function testHttpClientThrowsExceptionOnServerError()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionCode(500);

        $mockResponse = new Response(500, [], '{"message": "Internal Server Error"}');

        $this->mockGuzzle->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($this->mockConfig, $this->mockGuzzle);
        $httpClient->postJson('/auth', array('test' => 'data'));
    }

    public function testHttpClientHandlesEmptyErrorMessage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown error');

        $mockResponse = new Response(400, [], '{}');

        $this->mockGuzzle->expects($this->once())
            ->method('request')
            ->willReturn($mockResponse);

        $httpClient = new HttpClient($this->mockConfig, $this->mockGuzzle);
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

        $mockResponse = new Response(200, [], '{"success": true}');

        $this->mockGuzzle->expects($this->once())
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

        $httpClient = new HttpClient($mockConfig, $this->mockGuzzle);
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

        $mockResponse = new Response(200, [], '{"success": true}');

        $this->mockGuzzle->expects($this->once())
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

        $httpClient = new HttpClient($mockConfig, $this->mockGuzzle);
        $httpClient->postJson('/auth', array());
    }
}
