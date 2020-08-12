<?php

namespace FINDOLOGIC\Asurm\Request\Customer;

use FINDOLOGIC\Asurm\Request\Request;

class LoginRequest extends Request
{
    protected $method = 'POST';

    protected $uri = 'customer/login';

    public function __construct(string $username, string $password)
    {
        parent::__construct([
            'username' => $username,
            'password' => $password,
        ]);
    }
}
