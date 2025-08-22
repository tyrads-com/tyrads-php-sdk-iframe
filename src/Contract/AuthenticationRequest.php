<?php

namespace Tyrads\TyradsSdk\Contract;

class AuthenticationRequest
{
    /**
     * The age of the user.
     *
     * @var int
     */
    protected $age;

    /**
     * The gender of the user.
     *
     * @var int
     */
    protected $gender;

    /**
     * The publisher user ID.
     *
     * @var string
     */
    protected $publisherUserId;

    /**
     * The email address of the user.
     *
     * @var string
     */
    protected $email;

    /**
     * The phone number of the user.
     *
     * @var string
     */
    protected $phoneNumber;

    /**
     * Optional additional data for the authentication request.
     *
     * @var string
     */
    protected $sub1;

    /**
     * Optional additional data for the authentication request.
     *
     * @var string
     */
    protected $sub2;

    /**
     * Optional additional data for the authentication request.
     *
     * @var string
     */
    protected $sub3;

    /**
     * Optional additional data for the authentication request.
     *
     * @var string
     */
    protected $sub4;

    /**
     * Optional additional data for the authentication request.
     *
     * @var string
     */
    protected $sub5;

    /**
     * The user group associated with the authentication request.
     *
     * @var string
     */
    protected $userGroup;

    /**
     * The media source name.
     *
     * @var string
     */
    protected $mediaSourceName;

    /**
     * The media source ID.
     *
     * @var string
     */
    protected $mediaSourceId;

    /**
     * The media sub-source name.
     *
     * @var string
     */
    protected $mediaSubSourceId;

    /**
     * Incentivized status of the user.
     *
     * @var bool
     */
    protected $incentivized;

    /**
     * The media adset name.
     *
     * @var string
     */
    protected $mediaAdsetName;

    /**
     * The media adset ID.
     *
     * @var string
     */
    protected $mediaAdsetId;

    /**
     * The media creative name.
     *
     * @var string
     */
    protected $mediaCreativeName;

    /**
     * The media creative ID.
     *
     * @var string
     */
    protected $mediaCreativeId;

    /**
     * The media campaign name.
     *
     * @var string
     */
    protected $mediaCampaignName;

    /**
     * Constructor to initialize properties.
     * Only $publisherUserId is required.
     * The rest are optional and can be set later.
     *
     * @param string $publisherUserId
     * @param array $optionalParams (optional)
     */
    public function __construct($publisherUserId)
    {
        $this->publisherUserId = $publisherUserId;

        // For PHP 5 compatibility, no variadics. Accept optional array as 4th param.
        $args = func_get_args();
        if (isset($args[3]) && is_array($args[3])) {
            $this->setOptionalParams($args[3]);
        }
    }

    /**
     * Validate the properties of the AuthenticationRequest.
     *
     * @throws \InvalidArgumentException
     */
    public function validate()
    {
        // Validate publisherUserId
        if (empty($this->publisherUserId) || !is_string($this->publisherUserId)) {
            throw new \InvalidArgumentException('Publisher User ID cannot be empty and must be a string.');
        }

        // Validate age
        if ($this->age != null && (!is_int($this->age) || $this->age < 0)) {
            throw new \InvalidArgumentException('Age must be a non-negative integer.');
        }

        // Validate gender
        if ($this->gender != null && $this->gender !== 1 && $this->gender !== 2) {
            throw new \InvalidArgumentException('Gender must be either 1 (male) or 2 (female).');
        }

        // Validate email if present
        if (isset($this->email) && $this->email !== '') {
            if (!is_string($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                throw new \InvalidArgumentException('Invalid email format.');
            }
        }

        // Validate phoneNumber if present (basic check)
        if (isset($this->phoneNumber) && $this->phoneNumber !== '') {
            if (!is_string($this->phoneNumber) || !preg_match('/^\+?[0-9\- ]{7,20}$/', $this->phoneNumber)) {
                throw new \InvalidArgumentException('Invalid phone number format.');
            }
        }

        // Validate string fields if present
        $stringFields = array(
            'sub1',
            'sub2',
            'sub3',
            'sub4',
            'sub5',
            'userGroup',
            'mediaSourceName',
            'mediaSourceId',
            'mediaSubSourceId',
            'mediaAdsetName',
            'mediaAdsetId',
            'mediaCreativeName',
            'mediaCreativeId',
            'mediaCampaignName'
        );
        foreach ($stringFields as $field) {
            if (isset($this->$field) && $this->$field !== '' && !is_string($this->$field)) {
                throw new \InvalidArgumentException($field . ' must be a string.');
            }
        }

        // Validate incentivized if present (must be bool)
        if (isset($this->incentivized) && !is_bool($this->incentivized)) {
            throw new \InvalidArgumentException('incentivized must be a boolean.');
        }
    }

    /**
     * Set optional parameters for the authentication request.
     *
     * @param array $params
     */
    protected function setOptionalParams($params)
    {
        foreach ($params as $key => $value) {
            switch ($key) {
                case 'email':
                    $this->email = $value;
                    break;
                case 'phoneNumber':
                    $this->phoneNumber = $value;
                    break;
                case 'sub1':
                    $this->sub1 = $value;
                    break;
                case 'sub2':
                    $this->sub2 = $value;
                    break;
                case 'sub3':
                    $this->sub3 = $value;
                    break;
                case 'sub4':
                    $this->sub4 = $value;
                    break;
                case 'sub5':
                    $this->sub5 = $value;
                    break;
                case 'userGroup':
                    $this->userGroup = $value;
                    break;
                case 'mediaSourceName':
                    $this->mediaSourceName = $value;
                    break;
                case 'mediaSourceId':
                    $this->mediaSourceId = $value;
                    break;
                case 'mediaSubSourceId':
                    $this->mediaSubSourceId = $value;
                    break;
                case 'incentivized':
                    $this->incentivized = (bool)$value;
                    break;
                case 'mediaAdsetName':
                    $this->mediaAdsetName = $value;
                    break;
                case 'mediaAdsetId':
                    $this->mediaAdsetId = $value;
                    break;
                case 'mediaCreativeName':
                    $this->mediaCreativeName = $value;
                    break;
                case 'mediaCreativeId':
                    $this->mediaCreativeId = $value;
                    break;
                case 'mediaCampaignName':
                    $this->mediaCampaignName = $value;
                    break;
            }
        }
    }

    /**
     * Get the parsed data as an associative array.
     *
     * @return array
     */
    public function getParsedData()
    {
        $data = array(
            'publisherUserId' => $this->publisherUserId,
        );

        $optionalFields = array(
            'age' => $this->age,
            'gender' => $this->gender,
            'email' => $this->email,
            'phoneNumber' => $this->phoneNumber,
            'sub1' => $this->sub1,
            'sub2' => $this->sub2,
            'sub3' => $this->sub3,
            'sub4' => $this->sub4,
            'sub5' => $this->sub5,
            'userGroup' => $this->userGroup,
            'mediaSourceName' => $this->mediaSourceName,
            'mediaSourceId' => $this->mediaSourceId,
            'mediaSubSourceId' => $this->mediaSubSourceId,
            'incentivized' => $this->incentivized,
            'mediaAdsetName' => $this->mediaAdsetName,
            'mediaAdsetId' => $this->mediaAdsetId,
            'mediaCreativeName' => $this->mediaCreativeName,
            'mediaCreativeId' => $this->mediaCreativeId,
            'mediaCampaignName' => $this->mediaCampaignName,
        );

        foreach ($optionalFields as $key => $value) {
            if (isset($value) && $value !== '') {
                $data[$key] = $value;
            }
        }

        return $data;
    }
}
