<?php

use Tyrads\TyradsSdk\Configuration;

test('Configuration can be instantiated with required parameters', function () {
    $config = new Configuration('test_api_key', 'test_api_secret');
    
    expect($config->getApiKey())->toBe('test_api_key');
    expect($config->getApiSecret())->toBe('test_api_secret');
    expect($config->getLanguage())->toBe('en');
});

test('Configuration can be instantiated with custom language', function () {
    $config = new Configuration('test_api_key', 'test_api_secret', 'es');
    
    expect($config->getLanguage())->toBe('es');
});

test('Configuration returns correct API URL', function () {
    $config = new Configuration('test_api_key', 'test_api_secret');
    
    expect($config->getParsedApiUrl())->toBe('https://api.tyrads.com/v3.0');
});

test('Configuration returns correct SDK platform', function () {
    $config = new Configuration('test_api_key', 'test_api_secret');
    
    expect($config->getSdkPlatform())->toBe('Web');
});

test('Configuration returns correct iframe base URL', function () {
    $config = new Configuration('test_api_key', 'test_api_secret');
    
    expect($config->getSdkIframeBaseUrl())->toBe('https://sdk.tyrads.com');
});

test('Configuration returns SDK version from composer.json', function () {
    $config = new Configuration('test_api_key', 'test_api_secret');
    $version = $config->getSdkVersion();
    
    expect($version)->toBeString();
    expect($version)->not()->toBe('unknown');
});