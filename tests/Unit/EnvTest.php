<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Env;

class EnvTest extends TestCase
{
    protected $originalEnv;
    protected $originalServer;

    protected function setUp(): void
    {
        parent::setUp();
        // Save original environment
        $this->originalEnv = $_ENV;
        $this->originalServer = $_SERVER;
    }

    protected function tearDown(): void
    {
        // Restore original environment
        $_ENV = $this->originalEnv;
        $_SERVER = $this->originalServer;
        parent::tearDown();
    }

    public function testEnvCanBeInstantiated()
    {
        $env = new Env();
        $this->assertInstanceOf(Env::class, $env);
    }

    public function testEnvGetReturnsValueFromEnvVariable()
    {
        $_ENV['TEST_VAR'] = 'test_value_from_env';
        unset($_SERVER['TEST_VAR']);

        $env = new Env();
        $result = $env->get('TEST_VAR');

        $this->assertEquals('test_value_from_env', $result);
    }

    public function testEnvGetReturnsValueFromServerVariable()
    {
        unset($_ENV['TEST_VAR']);
        $_SERVER['TEST_VAR'] = 'test_value_from_server';

        $env = new Env();
        $result = $env->get('TEST_VAR');

        $this->assertEquals('test_value_from_server', $result);
    }

    public function testEnvGetPrefersEnvOverServer()
    {
        $_ENV['TEST_VAR'] = 'env_value';
        $_SERVER['TEST_VAR'] = 'server_value';

        $env = new Env();
        $result = $env->get('TEST_VAR');

        $this->assertEquals('env_value', $result);
    }

    public function testEnvGetReturnsNullForNonExistentVariable()
    {
        unset($_ENV['NONEXISTENT_VAR']);
        unset($_SERVER['NONEXISTENT_VAR']);

        $env = new Env();
        $result = $env->get('NONEXISTENT_VAR');

        $this->assertNull($result);
    }

    public function testEnvGetHandlesEmptyStringValue()
    {
        $_ENV['EMPTY_VAR'] = '';

        $env = new Env();
        $result = $env->get('EMPTY_VAR');

        $this->assertEquals('', $result);
    }

    public function testEnvGetHandlesZeroValue()
    {
        $_ENV['ZERO_VAR'] = '0';

        $env = new Env();
        $result = $env->get('ZERO_VAR');

        $this->assertEquals('0', $result);
    }

    public function testEnvGetHandlesFalseStringValue()
    {
        $_ENV['FALSE_VAR'] = 'false';

        $env = new Env();
        $result = $env->get('FALSE_VAR');

        $this->assertEquals('false', $result);
    }
}
