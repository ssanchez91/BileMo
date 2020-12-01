<?php

namespace App\Tests\Entity;


use App\Entity\User;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use App\DataFixtures\CustomerFixtures;
use App\Repository\CustomerRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use FixturesTrait;
    private $bootKernel;

    public function setUp(){
        $this->bootKernel = self::bootKernel();
    } 

    public function testValidateUserEntity()
    {
        $this->assertHasErrors($this->createUserEntity(), 0);
    }

    public function testNoValidateLastNameUserEntity()
    {
        $this->assertHasErrors($this->createUserEntity()->setLastname(''), 1);
    }

    public function testNoValidateFirstNameUserEntity()
    {
        $this->assertHasErrors($this->createUserEntity()->setFirstname(''), 1);
    }

    public function testNoValidateUsernameUserEntity()
    {
        $this->assertHasErrors($this->createUserEntity()->setUsername(''), 1);
    }

    public function testNoValidateEmailUserEntity()
    {
        $this->assertHasErrors($this->createUserEntity()->setEmail('test-test.com'), 1);
        $this->assertHasErrors($this->createUserEntity()->setEmail(''), 1);
    }

    /**
     * @return User
     */
    private function createUserEntity():User
    {
        $this->loadFixtures([CustomerFixtures::class]);
        
        /** @var@ Customer $customers */
        $customers = self::$container->get(CustomerRepository::class)->findAll();
        
        return (new User())
            ->setFirstname('John')
            ->setLastname('Doe')
            ->setUsername('jdoe')
            ->setEmail('jdoe@yopmail.fr')
            ->setCustomer($customers[rand(0,9)]);
    }    

    /**
     * @param User $user
     * @param int $number
     * @param array $groups
     */
    public function assertHasErrors(User $user, int $number = 0, $groups = ['Create'])
    {
        $this->bootKernel;
        $errors = self::$container->get('validator')->validate($user, $constraints = null, $groups );
        $messages =[];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error)
        {
            $messages[] = $error->getPropertyPath(). ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(',', $messages));
    }
}