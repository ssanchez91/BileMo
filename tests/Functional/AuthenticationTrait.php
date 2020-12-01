<?php

namespace App\Tests\Functional;

use DateInterval;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

/**
 * Trait AuthenticationTrait.
 */
trait AuthenticationTrait
{
    /**
     * Helper to access test Client.
     *
     * @var KernelBrowser
     */
    private $client;

    /**
     * Make a tested request with Client authentication by JWT.
     *
     * @param string     $email
     * @param string     $verb
     * @param string     $url
     * @param array|null $data
     *
     * @return void
     */
    public function requestAuthenticated(string $email, string $verb, string $url, ?array $data = null): void
    {
        $arrayParamUsername = array('username' => $email);
        $token = $this->client->getContainer()
            ->get('lexik_jwt_authentication.encoder')    
            ->encode($arrayParamUsername);

        $this->client->request(
            $verb,
            $url,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer '. $token,
                'CONTENT_TYPE' => 'application/json',
            ],
            $data ? json_encode($data) : null
        );
    }
}
