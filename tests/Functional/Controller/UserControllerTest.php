<?php

namespace App\Tests\Functional\Controller;

use Doctrine\ORM\EntityManager;
use App\DataFixtures\UserFixtures;
use App\DataFixtures\CustomerFixtures;
use App\Tests\Functional\AuthenticationTrait;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest.
 */
class UserControllerTest extends WebTestCase
{
    use AuthenticationTrait;
    use FixturesTrait;

    /**
     * Represent URI.
     *
     * @var string
     */
    const USERS_LIST_URI = '/api/customers/'.self::CUSTOMER_ID.'/users';

    /**
     * Represent a good customer Id.
     *
     * @var int
     */
    const CUSTOMER_ID = 1;

    /**
     * Represent a user Id who belongs to the customer.
     *
     * @var int
     */
    const USER_ID = 1;

    /**
     * Represent a user Id that does not belong to the customer 2.
     *
     * @var int
     */
    const BAD_USER_ID = 30;

    /**
     * Represent a user Id that does not belong to the customer 2.
     *
     * @var int
     */
    const USER_ID_NOT_EXIST = 999;

    /**
     * An ORM EntityManager Instance.
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Set up Client and EntityManager.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = $this->createClient(['environment' => 'test']);
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $this->entityManager->beginTransaction();
        $this->loadFixtures([CustomerFixtures::class, UserFixtures::class]);
    }

    /**
     * Test get users List to a client.
     *
     * @return void
     */
    public function testGetUsersList(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::USERS_LIST_URI
        );
        $content = $this->client->getResponse()->getContent();
        $content = json_decode($content, true);
        var_dump($content);
        $this->assertCount(5, $content['data']);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get users list of an other customer.
     *
     * @return void
     */
    public function testGetUsersNotYours(): void
    {
        $this->requestAuthenticated(
            'customer_1@yopmail.fr',
            'GET',
            self::USERS_LIST_URI
        );

        $this->assertSame(Response::HTTP_FORBIDDEN,$this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get user details for a user id that doesn't exit.
     *
     * @return void
     */
    public function testGetUserIdNotExist(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::USERS_LIST_URI.'/'.self::USER_ID_NOT_EXIST
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get users list with an invalid JWT Token.
     *
     * @return void
     */
    public function testGetUsersWithInvalidToken(): void
    {
        $this->requestAuthenticated(
            'invalid_customer@yopmail.fr',
            'GET',
            self::USERS_LIST_URI
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED,$this->client->getResponse()->getStatusCode());
    }
    
    /**
     * Test get details of an existing user who belongs to a client.
     *
     * @return void
     */
    public function testGetUser(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::USERS_LIST_URI.'/'.self::USER_ID
        );
        $content = $this->client->getResponse()->getContent();
        $content = json_decode($content, true);
        $this->assertArrayHasKey('id', $content);
        $this->assertArrayHasKey('lastname', $content);
        $this->assertArrayHasKey('firstname', $content);
        $this->assertArrayHasKey('username', $content);
        $this->assertArrayHasKey('email', $content);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get user details not that not yours.
     *
     * @return void
     */
    public function testGetUserNotYours(): void
    {
        $this->requestAuthenticated(
            'customer_1@yopmail.fr',
            'GET',
            self::USERS_LIST_URI.'/'.self::USER_ID
        );
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Create a new user.
     *
     * @return void
     */
    public function testCreateUser(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'POST',
            self::USERS_LIST_URI,
            [
                "firstname" => "John",
                "lastname" => "Doe",
                "username" => "jdoe75",
                "email" => "jdoe75@yopmail.fr"
            ]
        );
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $this->assertSame(Response::HTTP_CREATED,$this->client->getResponse()->getStatusCode());
    }

    /**
     * Create new user for a bad customer id.
     *
     * @return void
     */
    public function testCreateUserWithBadUserId(): void
    {
        $this->requestAuthenticated(
            'customer_1@yopmail.fr',
            'POST',
            self::USERS_LIST_URI,
            [
                "firstname" => "John",
                "lastname" => "Doe",
                "username" => "jdoe75",
                "email" => "jdoe75@yopmail.fr"
            ]
        );
        $this->assertSame(Response::HTTP_FORBIDDEN,$this->client->getResponse()->getStatusCode());
    }

    /**
     * Test violations when create a user.
     *
     * @return void
     */
    public function testCreateUserWithViolations(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'POST',
            self::USERS_LIST_URI,
            [
                "firstname" => "John",
                "lastname" => "Doe",
                "username" => "jdoe75",
                "email" => "jdoe75@yopmail"
            ]
        );
        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);
        $content = json_decode($content, true);
        $this->assertArrayHasKey('message', $content);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test delete of an existing user who belongs to a client.
     *
     * @return void
     */
    public function testDeleteUser(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'DELETE',
            self::USERS_LIST_URI.'/'.self::USER_ID
        );
        $this->assertSame(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }


    /**
     * Test delete of a user who does not belong to a client.
     *
     * @return void
     */
    public function testDeleteWrongUser(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'DELETE',
            self::USERS_LIST_URI.'/'.self::BAD_USER_ID
        );
      $this->assertSame(Response::HTTP_FORBIDDEN,$this->client->getResponse()->getStatusCode());
    }
}
