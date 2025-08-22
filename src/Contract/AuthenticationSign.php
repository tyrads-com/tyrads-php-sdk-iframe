<?php

namespace Tyrads\TyradsSdk\Contract;

class AuthenticationSign
{
    /**
     * The authentication token.
     *
     * @var string
     */
    protected $token;

    /**
     * The publisher user ID.
     *
     * @var string
     */
    protected $publisherUserId;

    public function __construct($token, $publisherUserId)
    {
        $this->token = $token;
        $this->publisherUserId = $publisherUserId;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getPublisherUserId()
    {
        return $this->publisherUserId;
    }
}
