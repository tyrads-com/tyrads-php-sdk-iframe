<?php

namespace Tyrads\TyradsSdk;

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
    public function __construct(
        Configuration $config,
    ) {
        // Initialize Guzzle client based on Guzzle version
        $guzzle = GuzzleCompatibility::isUsingGuzzle5()
            ? new \GuzzleHttp\Client()
            : new \GuzzleHttp\Client();

        $this->config = $config;
        $this->http = new HttpClient($config, $guzzle);
    }


    public function authenticate(
        $publisherUserId,
        $age,
        $gender
    ) {
        // validate $publisherUserId
        if (empty($publisherUserId)) {
            throw new \InvalidArgumentException('Publisher User ID cannot be empty.');
        }

        // validate $age
        if (!is_int($age) || $age < 0) {
            throw new \InvalidArgumentException('Age must be a non-negative integer.');
        }

        // validate $gender must be either 1 or 2
        if (!in_array($gender, [1, 2], true)) {
            throw new \InvalidArgumentException('Gender must be either 1 (male) or 2 (female).');
        }

        // Prepare the authentication data
        $data = [
            'publisherUserId' => $publisherUserId,
            'age' => $age,
            'gender' => $gender,
        ];

        // Send the authentication request
        $response = $this->http->postJson('/auth', $data);
        if (isset($response['data']['token'])) {
            return new Contract\AuthenticationSign(
                $response['data']['token'],
                $publisherUserId,
                $age,
                $gender
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
