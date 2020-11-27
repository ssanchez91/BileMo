<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\DataFixtures\DependentFixturesInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        
        for($i = 0; $i < 50; $i++)
        {
            $customer = $this->getReference(CustomerFixtures::CUSTOMER_REFERENCE.'customer_'.rand(0,9));

            $user = (new User())
            ->setFirstname('firstname_'.$i)
            ->setLastname('lastname_'.$i)
            ->setUsername('username_'.$i)
            ->setEmail('user_'.$i.'@yopmail.fr')
            ->setCustomer($customer);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on.
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class
        ];
    }
}
