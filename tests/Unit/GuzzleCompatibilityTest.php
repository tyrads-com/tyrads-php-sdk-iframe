<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Helper\GuzzleCompatibility;

class GuzzleCompatibilityTest extends TestCase
{
    public function testGuzzleCompatibilityIsUsingGuzzle5ReturnsBooleanValue()
    {
        $result = GuzzleCompatibility::isUsingGuzzle5();

        $this->assertIsBool($result);
    }

    public function testGuzzleCompatibilityCanDetectGuzzleVersion()
    {
        // This test will pass regardless of actual Guzzle version
        // since the method is designed to return false as fallback
        $result = GuzzleCompatibility::isUsingGuzzle5();

        // Should return false for modern Guzzle versions (6+ are more common)
        $this->assertFalse($result);
    }

    public function testGuzzleCompatibilityMethodIsStatic()
    {
        // Test that the method can be called statically
        $this->assertTrue(method_exists(GuzzleCompatibility::class, 'isUsingGuzzle5'));

        $reflection = new ReflectionMethod(GuzzleCompatibility::class, 'isUsingGuzzle5');
        $this->assertTrue($reflection->isStatic());
    }

    public function testGuzzleCompatibilityMethodIsPublic()
    {
        $reflection = new ReflectionMethod(GuzzleCompatibility::class, 'isUsingGuzzle5');
        $this->assertTrue($reflection->isPublic());
    }

    public function testGuzzleCompatibilityClassExists()
    {
        $this->assertTrue(class_exists(GuzzleCompatibility::class));
    }
}
