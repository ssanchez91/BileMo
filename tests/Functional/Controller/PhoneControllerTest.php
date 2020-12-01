<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\UserFixtures;
use App\DataFixtures\PhoneFixtures;
use App\DataFixtures\CustomerFixtures;
use App\Tests\Functional\AuthenticationTrait;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PhoneControllerTest.
 */
class PhoneControllerTest extends WebTestCase
{
    use AuthenticationTrait;
    use FixturesTrait;

    /**
     * URI
     *
     * @var string
     */
    const PHONES_LIST_URI = '/api/phones';

    /**
     * An Id phone that does not exist.
     *
     * @var int
     */
    const WRONG_PHONE_ID = 150;

    /**
     * Set up Client and load Phone Fixtures
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->client = $this->createClient(['environment' => 'test']);
        $this->loadFixtures([PhoneFixtures::class, CustomerFixtures::class, UserFixtures::class]);
    }

    /**
     * Test get the phones list With an invalid JWT Token.
     *
     * @return void
     */
    public function testGetPhonesWithAnInvalidToken(): void
    {
        $this->requestAuthenticated(
            'invalid@token.fr',
            'GET',
            self::PHONES_LIST_URI
        );

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }


    /**
     * Test get the phones list.
     *
     * @return void
     */
    public function testGetPhonesList(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::PHONES_LIST_URI
        );

        $content = $this->client->getResponse()->getContent();
        $content = json_decode($content, true);
        $this->assertCount(5, $content['data']);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get the phones list with queryparams.
     *
     * @return void
     */
    public function testGetPhonesListWithLimit(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::PHONES_LIST_URI.'?limit=2'
        );

        $content = $this->client->getResponse()->getContent();
        $content = json_decode($content, true);
        $this->assertCount(2, $content['data']);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Test get details of an existing phone.
     *
     * @return void
     */
    public function testGetExistingPhone(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::PHONES_LIST_URI.'/4'
        );
      $content = $this->client->getResponse()->getContent();
        $content = json_decode($content, true);
        $this->assertArrayHasKey('brand', $content);
        $this->assertArrayHasKey('model', $content);
        $this->assertArrayHasKey('price', $content);
        $this->assertArrayHasKey('color', $content);
        $this->assertArrayHasKey('screen_size', $content);
        $this->assertArrayHasKey('description', $content);        
        $this->assertArrayNotHasKey('email', $content);
        $this->assertSame(
            Response::HTTP_OK,
            $this->client->getResponse()->getStatusCode()
        );
    }

    /**
     * Test get details of a phone that does not exist.
     *
     * @return void
     */
    public function testGetWrongPhone(): void
    {
        $this->requestAuthenticated(
            'customer_0@yopmail.fr',
            'GET',
            self::PHONES_LIST_URI.'/'.self::WRONG_PHONE_ID
        );
      $this->assertSame(
            Response::HTTP_NOT_FOUND,
            $this->client->getResponse()->getStatusCode()
        );
    }
}