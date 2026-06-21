<?php

namespace Tyrads\TyradsSdk;

class Configuration
{
    /**
     * The base URL for the TyrAds SDK Iframe.
     *
     * @var string
     */
    const SDK_IFRAME_BASE_URL = 'https://sdk.tyrads.com';

    /**
     * The base URL for the TyrAds SDK API.
     *
     * @var string
     */
    const SDK_API_BASE_URL = 'https://api.tyrads.com';

    /**
     * The default SDK API version to use.
     * Callers can override this via the Configuration / TyrAdsSdk::make() $apiVersion argument.
     *
     * @var string
     */
    const SDK_API_VERSION = 'v4.0';

    /**
     * The platform for which the SDK is built.
     *
     * @var string
     */
    const SDK_PLATFORM = 'Web';

    /**
     * The API key for authentication.
     *
     * @var string
     */
    protected $apiKey;

    /**
     * The language for the SDK.
     *
     * @var string
     * Defaults to 'en' (English).
     */
    protected $language;

    /**
     * The API secret for authentication.
     *
     * @var string
     */
    protected $apiSecret;

    /**
     * The API version to use for requests.
     * Defaults to self::SDK_API_VERSION when not provided.
     *
     * @var string
     */
    protected $apiVersion;

    public function __construct($apiKey, $apiSecret, $language = 'en', $apiVersion = null)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->language = $language;
        $this->apiVersion = ($apiVersion !== null && $apiVersion !== '') ? $apiVersion : self::SDK_API_VERSION;
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Get the API secret.
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }

    /**
     * Get the base URL for the SDK API.
     *
     * @return string
     */
    public function getParsedApiUrl()
    {
        return self::SDK_API_BASE_URL . '/' . $this->apiVersion;
    }

    /**
     * Get the resolved API version (the override if provided, otherwise the default constant).
     *
     * @return string
     */
    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    /**
     * Get the auth endpoint path for the configured API version.
     *
     * v3.x and earlier expose POST {baseUrl}/{version}/auth.
     * v4.0 and later moved the auth route under the initialize controller:
     * POST {baseUrl}/{version}/initialize/auth.
     *
     * @return string
     */
    public function getAuthEndpoint()
    {
        // Strip a leading 'v' or 'V' so version_compare can read the numeric portion.
        $numeric = ltrim($this->apiVersion, 'vV');
        if (version_compare($numeric, '4.0', '>=')) {
            return '/initialize/auth';
        }
        return '/auth';
    }

    /**
     * Get the platform for which the SDK is built.
     *
     * @return string
     */
    public function getSdkPlatform()
    {
        return self::SDK_PLATFORM;
    }

    /**
     * Get the SDK Version from composer.json file.
     * @return string
     */
    public function getSdkVersion()
    {
        // Try to get version from composer.json if it exists (for development)
        if (file_exists(__DIR__ . '/../composer.json')) {
            $composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
            if (isset($composer['version'])) {
                return $composer['version'];
            }
        }

        // For Packagist installations, try to get version from Composer runtime
        // Note: Composer\InstalledVersions is only available in Composer 2.0+
        if (class_exists('\Composer\InstalledVersions')) {
            try {
                $version = \Composer\InstalledVersions::getVersion('tyrads/tyrads-sdk');
                return $version ?: 'dev-main';
            } catch (\Exception $e) {
                // Ignore and fall through to default
            }
        }

        // Fallback for development or when version cannot be determined
        return 'dev-main';
    }

    /**
     * Get the language for the SDK.
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Get the base URL for the SDK Iframe.
     *
     * v3.x keeps the legacy host (sdk.tyrads.com); every other major version is
     * served from a version-prefixed subdomain (v{major}.sdk.tyrads.com), e.g.
     * v4.x -> v4.sdk.tyrads.com, v5.x -> v5.sdk.tyrads.com.
     *
     * @return string
     */
    public function getSdkIframeBaseUrl()
    {
        $major = (int) ltrim($this->apiVersion, 'vV');
        if ($major === 3) {
            return self::SDK_IFRAME_BASE_URL;
        }
        return str_replace('https://', 'https://v' . $major . '.', self::SDK_IFRAME_BASE_URL);
    }

    /**
     * Whether the configured API version is v4 or later. v3 and earlier return
     * false; unrecognized versions are treated as the latest supported version
     * and return true.
     *
     * @return bool
     */
    public function isV4OrAbove()
    {
        $numeric = ltrim($this->apiVersion, 'vV');
        if ($numeric === '') {
            return true;
        }
        return version_compare($numeric, '4.0', '>=');
    }
}
