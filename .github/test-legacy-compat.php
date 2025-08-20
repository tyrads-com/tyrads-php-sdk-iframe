<?php
/**
 * Test script to simulate legacy PHP/Composer environments
 * This simulates the conditions found in PHP 5.6-7.1 with Composer v1
 */

// Include autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Import classes at top level for PHP 5.5+ compatibility
use Tyrads\TyradsSdk\TyrAdsSdk;
use Tyrads\TyradsSdk\Configuration;
use Tyrads\TyradsSdk\Contract\AuthenticationRequest;
use Tyrads\TyradsSdk\Helper\GuzzleCompatibility;

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Testing TyrAds SDK legacy compatibility...\n";

try {
    
    // Test 1: Configuration version detection without Composer\InstalledVersions
    echo "Test 1: Configuration version detection\n";
    $config = new Configuration('test_key', 'test_secret');
    $version = $config->getSdkVersion();
    echo "  ✓ SDK Version: $version\n";
    
    // Test 2: Guzzle compatibility detection
    echo "Test 2: Guzzle compatibility detection\n";
    $isGuzzle5 = GuzzleCompatibility::isUsingGuzzle5();
    echo "  ✓ Is Guzzle 5.x: " . ($isGuzzle5 ? 'Yes' : 'No') . "\n";
    
    // Test 3: SDK creation (this internally calls version detection)
    echo "Test 3: SDK instantiation\n";
    $sdk = TyrAdsSdk::make('test_key', 'test_secret');
    echo "  ✓ SDK instantiation successful\n";
    
    // Test 4: AuthenticationRequest
    echo "Test 4: AuthenticationRequest creation\n";
    $request = new AuthenticationRequest('test_user', 25, 1);
    echo "  ✓ AuthenticationRequest creation successful\n";
    
    // Test 5: iframe URL generation
    echo "Test 5: iFrame URL generation\n";
    $url = $sdk->iframeUrl('test_token');
    if (strpos($url, 'sdk.tyrads.com') !== false) {
        echo "  ✓ iFrame URL generation successful\n";
    } else {
        echo "  ✗ iFrame URL generation failed\n";
        exit(1);
    }
    
    echo "✓ All legacy compatibility tests passed!\n";
    
} catch (Exception $e) {
    echo "✗ Legacy compatibility test failed: " . $e->getMessage() . "\n";
    exit(1);
} catch (Error $e) {
    echo "✗ Legacy compatibility test failed: " . $e->getMessage() . "\n";
    exit(1);
}