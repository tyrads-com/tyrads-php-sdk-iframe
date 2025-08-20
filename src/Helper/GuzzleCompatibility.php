<?php

namespace Tyrads\TyradsSdk\Helper;

class GuzzleCompatibility
{
    /**
     * Check if the Guzzle version is 5.
     *
     * @return bool
     */
    public static function isUsingGuzzle5()
    {
        // Method 1: Try using Composer\InstalledVersions (Composer 2.0+)
        if (class_exists('\Composer\InstalledVersions')) {
            try {
                $version = \Composer\InstalledVersions::getVersion('guzzlehttp/guzzle');
                if ($version !== null) {
                    return version_compare($version, '6.0.0', '<');
                }
            } catch (\Exception $e) {
                // Fall through to next method
            }
        }
        
        // Method 2: Try to detect from class existence (more reliable for older setups)
        if (class_exists('\GuzzleHttp\Collection')) {
            // Guzzle 5.x has the Collection class, 6.x doesn't
            return true;
        }
        
        // Method 3: Try to detect from Client class methods
        if (class_exists('\GuzzleHttp\Client')) {
            $reflection = new \ReflectionClass('\GuzzleHttp\Client');
            if ($reflection->hasMethod('createRequest')) {
                // createRequest method exists in Guzzle 5.x but not 6.x+
                return true;
            }
        }
        
        // Method 4: Fallback - assume Guzzle 6+ if we can't determine
        // This is safer for modern installations
        return false;
    }
}
