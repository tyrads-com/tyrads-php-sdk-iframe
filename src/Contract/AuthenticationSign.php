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

    /**
     * The age of the user.
     *
     * @var int
     */
    protected $age;

    /**
     * The gender of the user.
     * 1 = male, 2 = female
     *
     * @var int
     */
    protected $gender;

    public function __construct($token, $publisherUserId, $age, $gender)
    {
        $this->token = $token;
        $this->publisherUserId = $publisherUserId;
        $this->age = $age;
        $this->gender = $gender;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getPublisherUserId()
    {
        return $this->publisherUserId;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function getGender()
    {
        return $this->gender;
    }
}
