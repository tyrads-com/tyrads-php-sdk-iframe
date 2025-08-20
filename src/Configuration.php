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
     * The SDK API version to use.
     *
     * @var string
     */
    const SDK_API_VERSION = 'v3.0';

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

    public function __construct(string $apiKey, string $apiSecret, $language = 'en')
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->language = $language;
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the API secret.
     *
     * @return string
     */
    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    /**
     * Get the base URL for the SDK API.
     *
     * @return string
     */
    public function getParsedApiUrl(): string
    {
        return self::SDK_API_BASE_URL . '/' . self::SDK_API_VERSION;
    }

    /**
     * Get the platform for which the SDK is built.
     *
     * @return string
     */
    public function getSdkPlatform(): string
    {
        return self::SDK_PLATFORM;
    }

    /**
     * Get the SDK Version from composer.json file.
     * @return string
     */
    public function getSdkVersion(): string
    {
        // Try to get version from composer.json if it exists (for development)
        if (file_exists(__DIR__ . '/../composer.json')) {
            $composer = json_decode(file_get_contents(__DIR__ . '/../composer.json'), true);
            if (isset($composer['version'])) {
                return $composer['version'];
            }
        }
        
        // For Packagist installations, try to get version from Composer runtime
        if (class_exists('\Composer\InstalledVersions')) {
            try {
                return \Composer\InstalledVersions::getVersion('tyrads/tyrads-sdk') ?: 'dev-main';
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
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * Get the base URL for the SDK Iframe.
     *
     * @return string
     */
    public function getSdkIframeBaseUrl(): string
    {
        return self::SDK_IFRAME_BASE_URL;
    }
}
