<?php

use PHPUnit\Framework\TestCase;
use Tyrads\TyradsSdk\Env;

class EnvTest extends TestCase
{
    public function testEnvCanBeInstantiated()
    {
        $env = new Env();
        $this->assertInstanceOf(Env::class, $env);
    }

    public function testEnvGetReturnsValueFromEnvVariable()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        $_ENV['TEST_VAR'] = 'test_value_from_env';
        unset($_SERVER['TEST_VAR']);

        $env = new Env();
        $result = $env->get('TEST_VAR');

        $this->assertEquals('test_value_from_env', $result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }

    public function testEnvGetReturnsValueFromServerVariable()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        unset($_ENV['TEST_VAR']);
        $_SERVER['TEST_VAR'] = 'test_value_from_server';

        $env = new Env();
        $result = $env->get('TEST_VAR');

        $this->assertEquals('test_value_from_server', $result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }

    public function testEnvGetPrefersEnvOverServer()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        $_ENV['TEST_VAR'] = 'env_value';
        $_SERVER['TEST_VAR'] = 'server_value';

        $env = new Env();
        $result = $env->get('TEST_VAR');

        $this->assertEquals('env_value', $result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }

    public function testEnvGetReturnsNullForNonExistentVariable()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        unset($_ENV['NONEXISTENT_VAR']);
        unset($_SERVER['NONEXISTENT_VAR']);

        $env = new Env();
        $result = $env->get('NONEXISTENT_VAR');

        $this->assertNull($result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }

    public function testEnvGetHandlesEmptyStringValue()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        $_ENV['EMPTY_VAR'] = '';

        $env = new Env();
        $result = $env->get('EMPTY_VAR');

        $this->assertEquals('', $result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }

    public function testEnvGetHandlesZeroValue()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        $_ENV['ZERO_VAR'] = '0';

        $env = new Env();
        $result = $env->get('ZERO_VAR');

        $this->assertEquals('0', $result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }

    public function testEnvGetHandlesFalseStringValue()
    {
        // Save original environment
        $originalEnv = $_ENV;
        $originalServer = $_SERVER;
        
        $_ENV['FALSE_VAR'] = 'false';

        $env = new Env();
        $result = $env->get('FALSE_VAR');

        $this->assertEquals('false', $result);
        
        // Restore original environment
        $_ENV = $originalEnv;
        $_SERVER = $originalServer;
    }
}
