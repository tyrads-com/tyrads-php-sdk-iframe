<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Configuration;

class ConfigurationTest extends TestCase
{
    public function testConfigurationCanBeInstantiatedWithRequiredParameters()
    {
        $config = new Configuration('test_api_key', 'test_api_secret');
        
        $this->assertEquals('test_api_key', $config->getApiKey());
        $this->assertEquals('test_api_secret', $config->getApiSecret());
        $this->assertEquals('en', $config->getLanguage());
    }

    public function testConfigurationCanBeInstantiatedWithCustomLanguage()
    {
        $config = new Configuration('test_api_key', 'test_api_secret', 'es');
        
        $this->assertEquals('es', $config->getLanguage());
    }

    public function testConfigurationReturnsCorrectApiUrl()
    {
        $config = new Configuration('test_api_key', 'test_api_secret');
        
        $this->assertEquals('https://api.tyrads.com/v3.0', $config->getParsedApiUrl());
    }

    public function testConfigurationReturnsCorrectSdkPlatform()
    {
        $config = new Configuration('test_api_key', 'test_api_secret');
        
        $this->assertEquals('Web', $config->getSdkPlatform());
    }

    public function testConfigurationReturnsCorrectIframeBaseUrl()
    {
        $config = new Configuration('test_api_key', 'test_api_secret');
        
        $this->assertEquals('https://sdk.tyrads.com', $config->getSdkIframeBaseUrl());
    }

    public function testConfigurationReturnsSdkVersionFromComposerJson()
    {
        $config = new Configuration('test_api_key', 'test_api_secret');
        $version = $config->getSdkVersion();
        
        $this->assertIsString($version);
        // Version should be a non-empty string and not 'unknown'
        $this->assertNotEmpty($version);
        $this->assertNotEquals('unknown', $version);
    }
}