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

        $this->assertTrue(is_string($version));
        // Version should be a non-empty string and not 'unknown'
        $this->assertNotEmpty($version);
        $this->assertNotEquals('unknown', $version);
    }

    public function testConfigurationHandlesNullLanguage()
    {
        $config = new Configuration('test_api_key', 'test_api_secret', null);

        $this->assertNull($config->getLanguage()); // Should be null as passed
    }

    public function testConfigurationHandlesEmptyStringLanguage()
    {
        $config = new Configuration('test_api_key', 'test_api_secret', '');

        $this->assertEquals('', $config->getLanguage());
    }

    public function testConfigurationHandlesDifferentLanguageCodes()
    {
        $languages = array('fr', 'de', 'it', 'pt', 'zh', 'ja');

        foreach ($languages as $lang) {
            $config = new Configuration('test_api_key', 'test_api_secret', $lang);
            $this->assertEquals($lang, $config->getLanguage());
        }
    }

    public function testConfigurationGettersReturnConsistentValues()
    {
        $config = new Configuration('consistent_key', 'consistent_secret', 'fr');

        // Test multiple calls return same values
        $this->assertEquals('consistent_key', $config->getApiKey());
        $this->assertEquals('consistent_key', $config->getApiKey()); // Second call

        $this->assertEquals('consistent_secret', $config->getApiSecret());
        $this->assertEquals('consistent_secret', $config->getApiSecret()); // Second call

        $this->assertEquals('fr', $config->getLanguage());
        $this->assertEquals('fr', $config->getLanguage()); // Second call
    }

    public function testConfigurationAcceptsSpecialCharactersInCredentials()
    {
        $config = new Configuration('key-with-dashes_and_underscores', 'secret$with@special#chars');

        $this->assertEquals('key-with-dashes_and_underscores', $config->getApiKey());
        $this->assertEquals('secret$with@special#chars', $config->getApiSecret());
    }

    public function testConfigurationUrlsAreHttps()
    {
        $config = new Configuration('test_key', 'test_secret');

        $this->assertStringStartsWith('https://', $config->getParsedApiUrl());
        $this->assertStringStartsWith('https://', $config->getSdkIframeBaseUrl());
    }

    public function testConfigurationConstantsExist()
    {
        $config = new Configuration('test_key', 'test_secret');

        // Test that the configuration returns expected static values
        $this->assertEquals('Web', $config->getSdkPlatform());
        $this->assertEquals('https://api.tyrads.com/v3.0', $config->getParsedApiUrl());
        $this->assertEquals('https://sdk.tyrads.com', $config->getSdkIframeBaseUrl());
    }
}
