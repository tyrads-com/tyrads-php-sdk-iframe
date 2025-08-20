<?php

namespace Tyrads\TyradsSdk;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Tyrads\TyradsSdk\Helper\GuzzleCompatibility;

class HttpClient
{
    /**
     * @var \Tyrads\TyradsSdk\Configuration
     */
    protected $config;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $guzzle;

    public function __construct(Configuration $config, ClientInterface $guzzle)
    {
        $this->config = $config;
        $this->guzzle = $guzzle;
    }

    /**
     * Send a POST request to Bugsnag.
     *
     * @param string $uri the uri to hit
     * @param array $options the request options
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function post($uri, $options = [])
    {
        $parsedUri = $this->config->getParsedApiUrl() . $uri;

        // Add X-API-Key header on request
        $options['headers']['X-API-Key'] = $this->config->getApiKey();
        $options['headers']['X-API-Secret'] = $this->config->getApiSecret();
        $options['headers']['X-SDK-Platform'] = $this->config->getSdkPlatform();
        $options['headers']['X-SDK-Version'] = $this->config->getSdkVersion();

        // Add query parameters if has language
        if ($this->config->getLanguage()) {
            $options['query']['lang'] = $this->config->getLanguage();
        }

        if (GuzzleCompatibility::isUsingGuzzle5()) {
            $response = $this->guzzle->post($parsedUri, $options);
        } else {
            $response = $this->guzzle->request('POST', $parsedUri, $options);
        }

        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 300) {
            $data = $this->normalizeResponse($response);
            $message = isset($data['json']['message']) ? $data['json']['message'] : 'Unknown error';
            throw new \RuntimeException($message, $statusCode);
        }

        return $response;
    }

    /**
     * Send a POST request with JSON data.
     *
     * @param string $uri the uri to hit
     * @param array $data the data to send as JSON
     *
     * @return array the normalized response containing raw body, headers, and JSON data
     */
    public function postJson($uri, $data = [])
    {
        $options = [
            'json' => $data,
        ];

        // Guzzle 5 does not support the 'json' option, so convert to 'body' and set headers manually
        if (GuzzleCompatibility::isUsingGuzzle5()) {
            $options['body'] = json_encode($data);
            unset($options['json']);
            $options['headers']['Content-Type'] = 'application/json';
        }

        $response = $this->post($uri, $options);
        return $this->normalizeResponse($response);
    }


    /**
     * Normalize the response into a consistent format.
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    protected function normalizeResponse(ResponseInterface $response)
    {
        // Normalize the response into [rawBody, header, json] based on Guzzle version
        if (GuzzleCompatibility::isUsingGuzzle5()) {
            return [
                'rawBody' => (string) $response->getBody(),
                'header' => $response->getHeaders(),
                'json' => json_decode((string) $response->getBody(), true),
            ];
        } else {
            return [
                'rawBody' => (string) $response->getBody(),
                'header' => $response->getHeaders(),
                'json' => json_decode((string) $response->getBody(), true),
            ];
        }
    }
}
