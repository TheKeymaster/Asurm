<?php

namespace FINDOLOGIC\Asurm;

use FINDOLOGIC\Asurm\Request\Customer\LoginRequest;
use FINDOLOGIC\Asurm\Request\Request;
use FINDOLOGIC\Asurm\Response\Customer\LoginResponse;
use FINDOLOGIC\Asurm\Response\Parser\JsonResponseParser;
use FINDOLOGIC\Asurm\Response\Response;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use InvalidArgumentException;

class Client
{
    public const OPTION_USERNAME = 'username';
    public const OPTION_PASSWORD = 'password';
    public const OPTION_TOKEN = 'token';
    public const OPTION_HTTP_CLIENT = 'httpClient';
    public const OPTION_API_URL = 'apiUrl';

    public const DEFAULT_API_URL = 'https://account.findologic.com/api/v1/';

    /**
     * @var string
     */
    private $token;

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * Use `Client::getInstance` to create a new Client instance.
     */
    public function __construct(string $token, GuzzleClient $client)
    {
        $this->token = $token;
        $this->client = $client;
    }

    /**
     * Creates a new Client instance.
     * ```
     * $client = Client::getInstance([
     *     Client::OPTION_USERNAME = '<YOUR USERNAME OR EMAIL>',
     *     Client::OPTION_PASSWORD = '<YOUR PASSWORD>',
     *     // Optionally only add the token.
     *     Client::OPTION_TOKEN = '<YOUR TOKEN>',
     * ]);
     * ```
     */
    public static function getInstance(array $options): self
    {
        $apiUrl = $options[self::OPTION_API_URL] ?? self::DEFAULT_API_URL;
        $httpClient = $options[self::OPTION_HTTP_CLIENT] ?? new GuzzleClient(['base_uri' => $apiUrl]);

        if (isset($options[self::OPTION_TOKEN])) {
            $token = $options[self::OPTION_TOKEN];

            return new Client($token, $httpClient);
        }

        if (!isset($options[self::OPTION_USERNAME]) || !isset($options[self::OPTION_PASSWORD])) {
            throw new InvalidArgumentException('A token, or username and password are required.');
        }

        $username = $options[self::OPTION_USERNAME];
        $password = $options[self::OPTION_PASSWORD];
        $response = static::doLogin($username, $password, $httpClient);

        return new Client($response->token, $httpClient);
    }

    public function sendRequest(Request $request): Response
    {
        //TODO: Implement Request and Response mechanism.
    }

    /**
     * Logs into the API with the given username and password.
     *
     * @param string $username
     * @param string $password
     * @return LoginResponse
     */
    public function login(string $username, string $password): LoginResponse
    {
        return static::doLogin($username, $password, $this->client);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    private static function doLogin(string $username, string $password, GuzzleClient $client): LoginResponse
    {
        $request = new LoginRequest($username, $password);
        $response = $client->send($request);

        if ($response->getStatusCode() >= 400) {
            throw new RequestException('Could not login.', $request, $response);
        }
        $responseBody = JsonResponseParser::parse($response->getBody()->__toString())['data'];

        return new LoginResponse($responseBody);
    }
}
