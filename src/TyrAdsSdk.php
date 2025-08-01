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
        $language = 'en'
    ): self {
        $env = new Env();

        $config = new Configuration($apiKey ?: $env->get(EnvVar::TYRADS_API_KEY), $apiSecret ?: $env->get(EnvVar::TYRADS_API_SECRET), $language);
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

        // Send the authentication request
        $response = $this->http->postJson('/auth', $data);
        if (isset($response['json']['data']['token'])) {
            return new Contract\AuthenticationSign(
                $response['json']['data']['token'],
                $data['publisherUserId'],
                $data['age'],
                $data['gender']
            );
        }

        return null;
    }

    /**
     * Generate the URL for the TyrAds SDK Iframe.
     *
     * @param \Tyrads\TyradsSdk\Contract\AuthenticationSign|string $authSignOrToken
     * @param string|null $deeplinkTo
     * @return string
     */
    public function iframeUrl($authSignOrToken, $deeplinkTo = null)
    {
        // Check if the input is an instance of AuthenticationSign or a string token
        if ($authSignOrToken instanceof Contract\AuthenticationSign) {
            $token = $authSignOrToken->getToken();
        } elseif (is_string($authSignOrToken)) {
            $token = $authSignOrToken;
        } else {
            throw new \InvalidArgumentException('Invalid argument: must be an instance of AuthenticationSign or a string token.');
        }

        $url = $this->config->getSdkIframeBaseUrl() . '?token=' . urlencode($token);
        if ($deeplinkTo !== null) {
            $url .= '&to=' . urlencode($deeplinkTo);
        }

        return $url;
    }
}
