<?php

namespace Tyrads\TyradsSdk\Helper;

use Composer\InstalledVersions;
use GuzzleHttp;

class GuzzleCompatibility
{
    /**
     * Check if the Guzzle version is 5.
     *
     * @return bool
     */
    public static function isUsingGuzzle5()
    {
        if (!class_exists(InstalledVersions::class)) {
            throw new \LogicException('Composer\InstalledVersions not available (Composer >= 2 required)');
        }
        $version = InstalledVersions::getVersion('guzzlehttp/guzzle');
        if ($version === null) {
            throw new \RuntimeException('guzzlehttp/guzzle is not installed');
        }

        return version_compare($version, '6.0.0', '<');
    }
}
