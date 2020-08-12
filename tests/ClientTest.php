<?php

namespace FINDOLOGIC\Asurm\Tests;

use FINDOLOGIC\Asurm\Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function invalidConfigOptionsProvider(): array
    {
        return [
            'empty config' => [
                'config' => []
            ],
            'missing password' => [
                'config' => [
                    'username' => 'someone@somewhere.io'
                ]
            ],
            'missing username' => [
                'config' => [
                    'password' => 'pretty secure.. not'
                ]
            ],
        ];
    }

    /**
     * @dataProvider invalidConfigOptionsProvider
     */
    public function testExceptionIsThrownWhenConfigOptionsAreMissing(array $config): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('A token, or username and password are required.');

        Client::getInstance($config);
    }

    public function testWhenTokenIsSuppliedItIsUsed(): void
    {
        $expectedToken = '1234';
        $client = Client::getInstance([
            'token' => $expectedToken
        ]);

        $this->assertInstanceOf(Client::class, $client);
        $this->assertSame($expectedToken, $client->getToken());
    }

    public function testProvidingUsernameAndPasswordWillSendALoginRequestToRetrieveTheToken(): void
    {
        $expectedToken = 'bestToken';

        $clientMock = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(GuzzleResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $streamMock = $this->getMockBuilder(Stream::class)
            ->disableOriginalConstructor()
            ->getMock();

        $streamMock->expects($this->once())->method('__toString')->willReturn(json_encode([
            'data' => [
                'user_id' => 1234,
                'token' => $expectedToken,
                'roles' => [
                    'ROLE_USER',
                    'ROLE_SUPER_ADMIN'
                ]
            ]
        ]));

        $responseMock->expects($this->once())->method('getStatusCode')->willReturn(201);
        $responseMock->expects($this->any())->method('getBody')->willReturn($streamMock);

        $clientMock->expects($this->once())
            ->method('send')
            ->willReturn($responseMock);

        $client = Client::getInstance([
            'httpClient' => $clientMock,
            'username' => 'a@b.com',
            'password' => 'adsfghjkl',
        ]);

        $this->assertSame($expectedToken, $client->getToken());
    }

    public function testErrorIsThrownInCaseLoginDataIsWrong(): void
    {
        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Could not login.');

        $clientMock = $this->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock = $this->getMockBuilder(GuzzleResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->any())->method('getStatusCode')->willReturn(403);

        $clientMock->expects($this->once())
            ->method('send')
            ->willReturn($responseMock);

        $client = Client::getInstance([
            'httpClient' => $clientMock,
            'username' => 'a@b.com',
            'password' => 'adsfghjkl',
        ]);
    }
}
