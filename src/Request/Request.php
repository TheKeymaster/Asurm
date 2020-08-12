<?php

namespace FINDOLOGIC\Asurm\Request;

use GuzzleHttp\Psr7\Request as GuzzleRequest;

abstract class Request extends GuzzleRequest
{
    /** @var string */
    protected $method;

    /** @var string */
    protected $uri;

    /** @var array */
    protected $headers;

    /** @var string|null */
    protected $body;

    public function __construct(array $headers = [], ?string $body = null)
    {
        parent::__construct($this->method, $this->uri, $headers, $body);
    }
}
