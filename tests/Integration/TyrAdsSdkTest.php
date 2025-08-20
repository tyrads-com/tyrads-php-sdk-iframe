<?php

use Tyrads\TyradsSdk\TyrAdsSdk;
use Tyrads\TyradsSdk\Configuration;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;
use Tyrads\TyradsSdk\Contract\AuthenticationSign;

test('TyrAdsSdk can be instantiated using make method', function () {
    $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
    
    expect($sdk)->toBeInstanceOf(TyrAdsSdk::class);
});

test('TyrAdsSdk can be instantiated with Configuration object', function () {
    $config = new Configuration('test_api_key', 'test_api_secret');
    $sdk = new TyrAdsSdk($config);
    
    expect($sdk)->toBeInstanceOf(TyrAdsSdk::class);
});

test('TyrAdsSdk can generate iframe URL with token string', function () {
    $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
    $token = 'test_token_123';
    
    $url = $sdk->iframeUrl($token);
    
    expect($url)->toContain('https://sdk.tyrads.com');
    expect($url)->toContain('token=test_token_123');
});

test('TyrAdsSdk can generate iframe URL with AuthenticationSign object', function () {
    $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
    $authSign = new AuthenticationSign('test_token_123', 'user123', 25, 1);
    
    $url = $sdk->iframeUrl($authSign);
    
    expect($url)->toContain('https://sdk.tyrads.com');
    expect($url)->toContain('token=test_token_123');
});

test('TyrAdsSdk can generate iframe URL with deeplink parameter', function () {
    $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
    $token = 'test_token_123';
    $deeplinkTo = 'surveys';
    
    $url = $sdk->iframeUrl($token, $deeplinkTo);
    
    expect($url)->toContain('https://sdk.tyrads.com');
    expect($url)->toContain('token=test_token_123');
    expect($url)->toContain('to=surveys');
});

test('TyrAdsSdk throws exception for invalid iframe URL parameter', function () {
    $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
    
    expect(function () use ($sdk) {
        $sdk->iframeUrl(123); // Invalid parameter type
    })->toThrow(InvalidArgumentException::class);
});

test('TyrAdsSdk URL encodes parameters correctly', function () {
    $sdk = TyrAdsSdk::make('test_api_key', 'test_api_secret');
    $token = 'test token with spaces';
    $deeplinkTo = 'path/with/special chars';
    
    $url = $sdk->iframeUrl($token, $deeplinkTo);
    
    // PHP's urlencode() converts spaces to + instead of %20
    expect($url)->toContain('token=test+token+with+spaces');
    expect($url)->toContain('to=path%2Fwith%2Fspecial+chars');
});

test('TyrAdsSdk authentication request is properly validated', function () {
    $authRequest = new AuthenticationRequest('user123', 25, 1);
    
    // Test that validation passes without throwing exception
    $authRequest->validate(); // This should not throw
    
    $data = $authRequest->getParsedData();
    expect($data)->toHaveKey('publisherUserId');
    expect($data)->toHaveKey('age');
    expect($data)->toHaveKey('gender');
});

test('TyrAdsSdk invalid authentication request throws validation error', function () {
    $invalidRequest = new AuthenticationRequest('', 25, 1); // Empty user ID
    
    expect(function () use ($invalidRequest) {
        $invalidRequest->validate();
    })->toThrow(InvalidArgumentException::class);
});