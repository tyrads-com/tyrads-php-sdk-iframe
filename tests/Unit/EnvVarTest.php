<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Enum\EnvVar;

class EnvVarTest extends TestCase
{
    public function testEnvVarClassExists()
    {
        $this->assertTrue(class_exists(EnvVar::class));
    }

    public function testEnvVarTyradsApiKeyConstantExists()
    {
        $this->assertTrue(defined('Tyrads\TyradsSdk\Enum\EnvVar::TYRADS_API_KEY'));
        $this->assertEquals('TYRADS_API_KEY', EnvVar::TYRADS_API_KEY);
    }

    public function testEnvVarTyradsApiSecretConstantExists()
    {
        $this->assertTrue(defined('Tyrads\TyradsSdk\Enum\EnvVar::TYRADS_API_SECRET'));
        $this->assertEquals('TYRADS_API_SECRET', EnvVar::TYRADS_API_SECRET);
    }

    public function testEnvVarConstantsAreStrings()
    {
        $this->assertTrue(is_string(EnvVar::TYRADS_API_KEY));
        $this->assertTrue(is_string(EnvVar::TYRADS_API_SECRET));
    }

    public function testEnvVarConstantsAreNotEmpty()
    {
        $this->assertNotEmpty(EnvVar::TYRADS_API_KEY);
        $this->assertNotEmpty(EnvVar::TYRADS_API_SECRET);
    }

    public function testEnvVarConstantsHaveExpectedValues()
    {
        // Test that constants match expected environment variable names
        $this->assertEquals('TYRADS_API_KEY', EnvVar::TYRADS_API_KEY);
        $this->assertEquals('TYRADS_API_SECRET', EnvVar::TYRADS_API_SECRET);
    }
}
