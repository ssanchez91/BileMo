<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Customer;
use App\Representation\Users;
use App\Service\TokenService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\QueryParam;

/**
 *  User Controller 
 */
class UserController extends AbstractFOSRestController
{    
    /**
     * EntityManagerInterface
     *
     * @var [type]
     */
    private $em;

    /**
     * TokenService
     * 
     * @var [type]
     */
    private $tokenService;

    /**
     * __construct
     *
     * @param EntityManagerInterface $em
     * @param TokenService $tokenService
     */
    public function __construct(EntityManagerInterface $em, TokenService $tokenService)
    {
        $this->em = $em;
        $this->tokenService = $tokenService;
    }

    /**
     * Return the customer' users
     * 
     * @Rest\Get(
     *     path = "/customers/{id}/users",
     *     name = "api_customers_users_list",
     *     requirements = {"id"="\d+"}
     * )
     * 
     * @Rest\View()
     *      
     * @ParamConverter("customer", options={"mapping":{"id":"id"}})
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
    public function listAction(Customer $customer, Request $request, UserRepository $userRepository, ParamFetcher $paramFetcher)
    {
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            return new Response(json_encode(['code'=>403, 'message'=>"Forbidden Access : this customer account is not yours"]), Response::HTTP_FORBIDDEN);
        }

        $paginator =  $userRepository->listAll($paramFetcher->get('order'), $paramFetcher->get('limit'), $paramFetcher->get('offset'), $customer);        
        
        return new Users($paginator, $customer);
    }

    /**
     * Return user' details for id customer in url path
     * 
     * @Rest\Get(
     *      path="/customers/{customerId}/users/{userId}",
     *      name="api_users_show",
     *      requirements = {"customerId"="\d+", "userId"="\d+"}
     * )
     * 
     * @ParamConverter("customer", options={"mapping": {"customerId" : "id"}})
     * 
     * @ParamConverter("user", options={"mapping": {"userId" : "id"}})
     * 
     * @Rest\View(StatusCode = 200)
     * 
     * 
     */
    public function showAction(Customer $customer, User $user, Request $request)
    {    
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            return new Response(json_encode(['code'=>403, 'message'=>"Forbidden Access : this customer account is not yours"]), Response::HTTP_FORBIDDEN);
        }    

        if($customer != $user->getCustomer())
        {
            return new Response(json_encode(['code'=>403, 'message'=>"Forbidden Access : this user doesn't yours"]), Response::HTTP_FORBIDDEN);
        }

        return $user;        
    }

    /**
     * Add a new user for customer id in url path
     * 
     * @Rest\Post(
     *      path = "/customers/{id}/users",
     *      name = "api_customer_users_create",
     *      requirements = {"id"="\d+"}
     * )
     * 
     * @Rest\View(StatusCode = 201)
     * 
     * @ParamConverter("user", converter="fos_rest.request_body", options={"validator" : { "groups" : "Create" }}) 
     * @ParamConverter("customer", options={"mapping" : { "id" : "id" }}) 
     * 
     */
    public function createAction(Customer $customer, User $user, ConstraintViolationList $violations, Request $request) 
    {
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            return new Response(json_encode(['code'=>403, 'message'=>"Forbidden Access : this customer account is not yours"]), Response::HTTP_FORBIDDEN);
        }
        
        if(count($violations) > 0)
        {
            $message = "The JSON sent contains invalid Data: ";  
            foreach($violations as $violation)
            {
                $message .= sprintf('Field %s: %s ', $violation->getPropertyPath(), $violation->getMessage());
            }
            $response = json_encode(['code'=>400, 'message'=>$message]);
            return new Response($response, Response::HTTP_BAD_REQUEST);
        }

        $user->setCustomer($customer);        
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * Delete a specific user.
     * 
     * @Rest\Delete(
     *      path="/customers/{customerId}/users/{userId}",
     *      name="api_users_delete",
     *      requirements = {"customerId"="\d+", "userId"="\d+"}
     * )
     * 
     * @Rest\View(StatusCode = 204)
     * 
     * @ParamConverter(
     *     "customer", options={"id" = "customerId"}
     * )
     * 
     * @ParamConverter("user", options={"id" = "userId"}
     * )
     * 
     * 
     */
    public function deleteAction(Customer $customer, User $user, Request $request)
    {
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            return new Response(json_encode(['code'=>403, 'message'=>"Forbidden Access : this customer account is not yours"]), Response::HTTP_FORBIDDEN);
        }

        if($customer != $user->getCustomer())
        {
            return new Response(json_encode(['code'=>403, 'message'=>"Forbidden Access : this user doesn't yours"]), Response::HTTP_FORBIDDEN);
        }
        
        $this->em->remove($user);
        $this->em->flush();
        return;
    }
}
