<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Repository\PhoneRepository;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\AbstractFOSRestController;

/**
 * PhoneController class
 */
class PhoneController extends AbstractFOSRestController
{
    /**
     * Return a list of Phone resource.
     * 
     * @Get(
     *      path="/phones",
     *      name="api_phones_list"
     * )
     * 
     * @View(statusCode = 200) 
     * 
     */
    public function listAction(PhoneRepository $phoneRepository)
    {  
        return $phoneRepository->findAll();
    }

    /**
     * Return Phone details.
     * 
     * @Get(
     *      path="/phones/{id}",
     *      name="api_phones_show",
     *      requirements = {"id"="\d+"}
     * )
     * 
     * @View(statusCode = 200) 
     * 
     */
    public function showAction(Phone $phone): Phone
    {
        return $phone;
    }
}