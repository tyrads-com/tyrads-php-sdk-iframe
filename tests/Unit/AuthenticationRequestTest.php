<?php

use Tyrads\TyradsSdk\Contract\AuthenticationRequest;

test('AuthenticationRequest can be instantiated with required parameters', function () {
    $request = new AuthenticationRequest('user123', 25, 1);
    
    $data = $request->getParsedData();
    expect($data['publisherUserId'])->toBe('user123');
    expect($data['age'])->toBe(25);
    expect($data['gender'])->toBe(1);
});

test('AuthenticationRequest can be instantiated with optional parameters', function () {
    $optionalParams = array(
        'email' => 'test@example.com',
        'phoneNumber' => '+1234567890',
        'sub1' => 'sub1_value',
        'userGroup' => 'vip'
    );
    
    $request = new AuthenticationRequest('user123', 25, 1, $optionalParams);
    
    $data = $request->getParsedData();
    expect($data['email'])->toBe('test@example.com');
    expect($data['phoneNumber'])->toBe('+1234567890');
    expect($data['sub1'])->toBe('sub1_value');
    expect($data['userGroup'])->toBe('vip');
});

test('AuthenticationRequest validates required parameters correctly', function () {
    $request = new AuthenticationRequest('user123', 25, 1);
    
    // Test that validation passes without throwing exception
    $request->validate(); // This should not throw
    
    expect(true)->toBe(true); // Simple assertion to mark test as passed
});

test('AuthenticationRequest throws exception for empty publisher user ID', function () {
    $request = new AuthenticationRequest('', 25, 1);
    
    expect(function () use ($request) {
        $request->validate();
    })->toThrow(InvalidArgumentException::class);
});

test('AuthenticationRequest throws exception for invalid age', function () {
    $request = new AuthenticationRequest('user123', -1, 1);
    
    expect(function () use ($request) {
        $request->validate();
    })->toThrow(InvalidArgumentException::class);
});

test('AuthenticationRequest throws exception for invalid gender', function () {
    $request = new AuthenticationRequest('user123', 25, 3);
    
    expect(function () use ($request) {
        $request->validate();
    })->toThrow(InvalidArgumentException::class);
});

test('AuthenticationRequest validates email format when provided', function () {
    $request = new AuthenticationRequest('user123', 25, 1, array('email' => 'invalid-email'));
    
    expect(function () use ($request) {
        $request->validate();
    })->toThrow(InvalidArgumentException::class);
});

test('AuthenticationRequest validates phone number format when provided', function () {
    $request = new AuthenticationRequest('user123', 25, 1, array('phoneNumber' => 'invalid'));
    
    expect(function () use ($request) {
        $request->validate();
    })->toThrow(InvalidArgumentException::class);
});

test('AuthenticationRequest accepts valid email format', function () {
    $request = new AuthenticationRequest('user123', 25, 1, array('email' => 'test@example.com'));
    
    // Test that validation passes without throwing exception
    $request->validate(); // This should not throw
    
    expect(true)->toBe(true); // Simple assertion to mark test as passed
});

test('AuthenticationRequest accepts valid phone number format', function () {
    $request = new AuthenticationRequest('user123', 25, 1, array('phoneNumber' => '+1-234-567-8900'));
    
    // Test that validation passes without throwing exception
    $request->validate(); // This should not throw
    
    expect(true)->toBe(true); // Simple assertion to mark test as passed
});

test('AuthenticationRequest excludes empty optional fields from parsed data', function () {
    $request = new AuthenticationRequest('user123', 25, 1, array('email' => '', 'sub1' => 'value'));
    
    $data = $request->getParsedData();
    expect($data)->not()->toHaveKey('email');
    expect($data)->toHaveKey('sub1');
    expect($data['sub1'])->toBe('value');
});