<?php

namespace App\DataFixtures;

use App\Entity\Phone;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class PhoneFixtures extends Fixture
{
    /**
     * An array which represent data to load.
     *
     * @var array
     */
    private $data = 
    [
        [
            'brand' => 'Samsung',
            'model' => 'S10',
            'price' => 879.90,
            'color' => 'Black',
            'screenSize' => '6,6"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Samsung',
            'model' => 'S10',
            'price' => 899.90,
            'color' => 'White',
            'screenSize' => '6,6"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Samsung',
            'model' => 'S10+',
            'price' => 999.90,
            'color' => 'Black',
            'screenSize' => '7,2"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Samsung',
            'model' => 'S10+',
            'price' => 1049.90,
            'color' => 'White',
            'screenSize' => '7,2"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Samsung',
            'model' => 'Note 10',
            'price' => 1290.90,
            'color' => 'Black',
            'screenSize' => '6,6"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Xiaomi',
            'model' => 'Redmi Note 8',
            'price' => 179.90,
            'color' => 'Black',
            'screenSize' => '6,2"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Xiaomi',
            'model' => 'Redmi Note 8 Pro',
            'price' => 209.90,
            'color' => 'Green',
            'screenSize' => '6,8"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Xiaomi',
            'model' => 'Mi 10',
            'price' => 550.00,
            'color' => 'Grey',
            'screenSize' => '6,67"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],[
            'brand' => 'Xiaomi',
            'model' => 'Mi 10T Pro',
            'price' => 359.90,
            'color' => 'Black',
            'screenSize' => '6,67"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Xiaomi',
            'model' => 'Mi 10 Light',
            'price' => 279.90,
            'color' => 'White',
            'screenSize' => '6,57"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ],
        [
            'brand' => 'Iphone',
            'model' => 'X',
            'price' => 1179.90,
            'color' => 'Black',
            'screenSize' => '5,59"',
            'description' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Explicabo eveniet quo repellendus, deserunt iste facilis rem saepe quaerat natus perferendis obcaecati veniam soluta quisquam, est pariatur recusandae. Dolorem, laudantium dignissimos.'
        ]
    ];

    /**
     * Load fixtures in phone table
     *
     * @param ObjectManager $manager
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        
        foreach($this->data as $row)
        {
            $phone = (new Phone())
                ->setBrand($row['brand'])
                ->setModel($row['model'])
                ->setPrice($row['price'])
                ->setColor($row['color'])
                ->setScreenSize($row['screenSize'])
                ->setDescription($row['description']);

            $manager->persist($phone);    
        }    

        $manager->flush();
    }
}
