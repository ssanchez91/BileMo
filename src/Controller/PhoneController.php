<?php

namespace App\Controller;

use App\Entity\Phone;
use App\Representation\Phones;
use App\Repository\PhoneRepository;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\AbstractFOSRestController;


/**
 * PhoneController class
 */
class PhoneController extends AbstractFOSRestController
{
    /**
     * Return a list of Phone resource.
     * 
     * @Get(path = "/phones",
     *      name = "api_phones_list"
     * ) 
     * 
     * @View(StatusCode = 200) 
     * 
     * @QueryParam(
     *     name="brand",
     *     requirements="[A-Za-z]*",
     *     nullable=true,
     *     description="The brand to search for."
     * )
     * 
     * @QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="asc",
     *     description="Sort order (asc or desc)"
     * )
     * 
     * @QueryParam(
     *     name="limit",
     *     requirements="\d+",
     *     default="5",
     *     description="Max number of phones per page."
     * )
     * 
     * @QueryParam(
     *     name="offset",
     *     requirements="\d+",
     *     default="1",
     *     description="The pagination offset"
     * )
     * 
     */
    public function listAction(PhoneRepository $phoneRepository, ParamFetcher $paramFetcher)
    {  
        $paginator =  $phoneRepository->listAll($paramFetcher->get('brand'), $paramFetcher->get('order'), $paramFetcher->get('limit'), $paramFetcher->get('offset'));        

        return new Phones($paginator);
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
     * @View(StatusCode = 200) 
     * 
     */
    public function showAction(Phone $phone): Phone
    {
        return $phone;
    }
}