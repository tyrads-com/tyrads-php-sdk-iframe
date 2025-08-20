<?php
/**
 * Basic compatibility test for the TyrAds SDK
 * Tests that the SDK can be loaded and basic functionality works
 */

// Include autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Import classes at top level for PHP 5.5+ compatibility
use Tyrads\TyradsSdk\TyrAdsSdk;
use Tyrads\TyradsSdk\Configuration;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Testing TyrAds SDK compatibility...\n";

try {
    
    // Test SDK creation
    $sdk = TyrAdsSdk::make('test_key', 'test_secret');
    echo "✓ SDK instantiation successful\n";
    
    // Test Configuration
    $config = new Configuration('test_key', 'test_secret');
    echo "✓ Configuration creation successful\n";
    
    // Test AuthenticationRequest
    $request = new AuthenticationRequest('test_user', 25, 1);
    echo "✓ AuthenticationRequest creation successful\n";
    
    // Test iframe URL generation (should not make HTTP calls)
    $url = $sdk->iframeUrl('test_token');
    if (strpos($url, 'sdk.tyrads.com') !== false) {
        echo "✓ iFrame URL generation successful\n";
    } else {
        echo "✗ iFrame URL generation failed\n";
        exit(1);
    }
    
    echo "✓ All compatibility tests passed!\n";
    
} catch (Exception $e) {
    echo "✗ Compatibility test failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Error $e) {
    echo "✗ Compatibility test failed: " . $e->getMessage() . "\n";
    exit(1);
}