<?php

namespace Tyrads\TyradsSdk;

use Tyrads\TyradsSdk\Contract\AuthenticationRequest;
use Tyrads\TyradsSdk\Enum\EnvVar;
use Tyrads\TyradsSdk\Helper\GuzzleCompatibility;

class TyrAdsSdk
{
    /**
     * @var \Tyrads\TyradsSdk\Configuration
     */
    protected $config;

    /**
     * @var \Tyrads\TyradsSdk\HttpClient
     */
    protected $http;

    public static function make(
        $apiKey = null,
        $apiSecret = null,
        $language = 'en',
        $apiVersion = null
    ) {
        $env = new Env();

        $config = new Configuration(
            $apiKey ?: $env->get(EnvVar::TYRADS_API_KEY),
            $apiSecret ?: $env->get(EnvVar::TYRADS_API_SECRET),
            $language,
            $apiVersion
        );
        return new self($config);
    }

    /**
     * TyrAdsSdk constructor.
     *
     * @param \Tyrads\TyradsSdk\Configuration $config
     */
    public function __construct(Configuration $config)
    {
        // Initialize Guzzle client based on Guzzle version
        $guzzle = GuzzleCompatibility::isUsingGuzzle5()
            ? new \GuzzleHttp\Client()
            : new \GuzzleHttp\Client();

        $this->config = $config;
        $this->http = new HttpClient($config, $guzzle);
    }


    public function authenticate(AuthenticationRequest $request)
    {
        $request->validate();

        // Prepare the authentication data
        $data = $request->getParsedData();

        // Send the authentication request to the version-appropriate endpoint
        // (v3.x: /auth, v4.0+: /initialize/auth).
        $response = $this->http->postJson($this->config->getAuthEndpoint(), $data);
        if (isset($response['json']['data']['token'])) {
            return new Contract\AuthenticationSign(
                $response['json']['data']['token'],
                $data['publisherUserId']
            );
        }

        return null;
    }

    /**
     * Generate the URL for the TyrAds SDK Iframe.
     *
     * @param \Tyrads\TyradsSdk\Contract\AuthenticationSign|string $authSignOrToken
     * @param string|null $deeplinkTo
     * @param int|null $placementId Optional placement ID. v4+ only.
     * @return string
     * @throws \InvalidArgumentException
     */
    public function iframeUrl($authSignOrToken, $deeplinkTo = null, $placementId = null)
    {
        // Check if the input is an instance of AuthenticationSign or a string token
        if ($authSignOrToken instanceof Contract\AuthenticationSign) {
            $token = $authSignOrToken->getToken();
        } elseif (is_string($authSignOrToken)) {
            $token = $authSignOrToken;
        } else {
            throw new \InvalidArgumentException('Invalid argument: must be an instance of AuthenticationSign or a string token.');
        }

        $this->assertValidPlacementId($placementId);

        $url = $this->config->getSdkIframeBaseUrl() . '?token=' . urlencode($token);
        if ($deeplinkTo !== null) {
            $url .= '&to=' . urlencode($deeplinkTo);
        }
        if ($placementId !== null) {
            $url .= '&placementId=' . $placementId;
        }

        return $url;
    }

    /**
     * Generate the URL for the TyrAds Premium Widget.
     *
     * @param \Tyrads\TyradsSdk\Contract\AuthenticationSign|string $authSignOrToken
     * @param string|null $name
     * @param int|null $placementId Optional placement ID. v4+ only.
     * @return string
     * @throws \InvalidArgumentException
     */
    public function iframePremiumWidget($authSignOrToken, $name = null, $placementId = null)
    {
        // Check if the input is an instance of AuthenticationSign or a string token
        if ($authSignOrToken instanceof Contract\AuthenticationSign) {
            $token = $authSignOrToken->getToken();
        } elseif (is_string($authSignOrToken)) {
            $token = $authSignOrToken;
        } else {
            throw new \InvalidArgumentException('Invalid argument: must be an instance of AuthenticationSign or a string token.');
        }

        $this->assertValidPlacementId($placementId);

        $url = $this->config->getSdkIframeBaseUrl() . '/widget?token=' . urlencode($token);
        if ($name !== null) {
            $url .= '&name=' . urlencode($name);
        }
        if ($placementId !== null) {
            $url .= '&placementId=' . $placementId;
        }

        return $url;
    }

    /**
     * Ensures the supplied placementId is either null or a positive integer
     * targeting an iframe version that supports it (v4+).
     *
     * @param mixed $placementId
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function assertValidPlacementId($placementId)
    {
        if ($placementId === null) {
            return;
        }
        if (!is_int($placementId) || $placementId <= 0) {
            throw new \InvalidArgumentException('Invalid placementId argument: must be a positive integer or null.');
        }
        if (!$this->config->isV4OrAbove()) {
            throw new \InvalidArgumentException('placementId is only supported on iframe v4 and above.');
        }
    }
}
