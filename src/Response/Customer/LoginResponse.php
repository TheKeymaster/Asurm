<?php

namespace FINDOLOGIC\Asurm\Response\Customer;

use FINDOLOGIC\Asurm\Definitions\Role;
use FINDOLOGIC\Asurm\Response\Response;

class LoginResponse extends Response
{
    /**
     * @see Role
     *
     * @var string[]
     */
    public $roles;

    /**
     * @var string
     */
    public $token;

    /**
     * @var int
     */
    public $userId;

    public function __construct(array $rawResponse)
    {
        $this->roles = $rawResponse['roles'];
        $this->token = $rawResponse['token'];
        $this->userId = $rawResponse['user_id'];
    }
}
