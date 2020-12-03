<?php

namespace App\Controller;


use App\Entity\User;
use App\Entity\Customer;
use App\Representation\Users;
use App\Service\TokenService;
use OpenApi\Annotations as OA;
use App\Repository\UserRepository;
use App\Exception\ForbiddenException;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use App\Exception\ResourceNoValidateException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\Validator\ConstraintViolationList;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * User Controller
 * 
 * @OA\Tag(name="Users")
 * 
 * 
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
     * 
     * @Rest\Get(
     *     path = "/customers/{id}/users",
     *     name = "api_customers_users_list",
     *     requirements = {"id"="\d+"}
     * ) 
     * 
     * @Cache(lastModified="customer.getUpdatedAt()", public=true, Etag="'Customer' ~ customer.getId() ~ customer.getUpdatedAt().getTimestamp()", Expires="+1 day")
     * 
     * @Rest\View()
     *      
     * @ParamConverter("customer", options={"mapping":{"id":"id"}})
     * 
     * @QueryParam(
     *     name="order",
     *     requirements="asc|desc",
     *     default="desc",
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
     * @OA\Parameter(
     *    name="id",
     *    in="path",
     *    description="ID of customer that needs to be used",
     *    required=true,
     *    @OA\Schema(
     *        type="integer",
     *        format="int64"
     *    )
     *  )
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns user details",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )     
     * )
     * 
     * @OA\Response(
     *     response = 401,
     *     description = "You must use a valid token to complete this request"
     * )
     * 
     * @OA\Response(
     *     response = 403,
     *     description = "Forbidden access to this content"
     * )
     * 
     * @OA\Response(
     *     response = 404,
     *     description = "This resource doesn't exist !"
     * )
     * 
     */
    public function listAction(Customer $customer, Request $request, UserRepository $userRepository, ParamFetcher $paramFetcher)
    {    
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            throw new ForbiddenException("Forbidden Access : this customer account is not yours");
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
     * @Cache(Expires="+1 day", lastModified="user.getCreatedAt()", Etag="'User' ~ user.getId() ~ user.getCreatedAt().getTimestamp()")
     * 
     * @ParamConverter("customer", options={"mapping": {"customerId" : "id"}})
     * 
     * @ParamConverter("user", options={"mapping": {"userId" : "id"}})
     * 
     * @Rest\View(StatusCode = 200)
     * 
     * @OA\Parameter(
     *    name="customerId",
     *    in="path",
     *    description="ID of customer that needs to be used",
     *    required=true,
     *    @OA\Schema(
     *        type="integer",
     *        format="int64"
     *    )
     *  )
     * 
     * @OA\Parameter(
     *    name="userId",
     *    in="path",
     *    description="ID of user that you want showing",
     *    required=true,
     *    @OA\Schema(
     *        type="integer",
     *        format="int64"
     *    )
     *  )
     * 
     *  @OA\Response(
     *     response=200,
     *     description="Returns user details",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )     
     * )
     * 
     * @OA\Response(
     *     response = 401,
     *     description = "You must use a valid token to complete this request"
     * )
     * 
     * @OA\Response(
     *     response = 403,
     *     description = "Forbidden access to this content"
     * )
     * 
     * @OA\Response(
     *     response = 404,
     *     description = "This resource doesn't exist !"
     * )
     * 
     * 
     */
    public function showAction(Customer $customer, User $user, Request $request)
    {    
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            throw new ForbiddenException("Forbidden Access : this customer account is not yours");
        }    

        if($customer != $user->getCustomer())
        {
            throw new ForbiddenException("Forbidden Access : this user doesn't yours");
        }

        return $user;        
    }

    /**
     * Add a new user for customer id in url path
     * 
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
     * @OA\RequestBody(
     *     required=true,
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(property="firstname", type="string", example="John"),
     *         @OA\Property(property="lastname", description="The lastname of the new user.", type="string", example="Doe"),
     *         @OA\Property(property="username", description="The username of the new user.", type="string", example="jdoe91"),
     *         @OA\Property(property="email", description="Email address of the new user.", type="string", format="email", example="j.doe91@yopmail.fr")
     *       )
     *     )
     *   )
     * 
     * @OA\Parameter(
     *    name="id",
     *    in="path",
     *    description="ID of customer that needs to be used",
     *    required=true,
     *    @OA\Schema(
     *        type="integer",
     *        format="int64"
     *    )
     *  )
     * 
     * @OA\Response(
     *     response = 201,
     *     description = "User successfully added to the Client",
     *     @Model(type=User::class)
     * )
     * @OA\Response(
     *     response = 400,
     *     description = "Bad data sent, check fields and try again"
     * )
     * @OA\Response(
     *     response = 401,
     *     description = "You must use a valid token to complete this request"
     * )
     * @OA\Response(
     *     response = 403,
     *     description = "Forbidden access to this content"
     * )
     * 
     * @OA\Response(
     *     response = 404,
     *     description = "This resource doesn't exist !"
     * )
     * 
     */
    public function createAction(Customer $customer, User $user, ConstraintViolationList $violations, Request $request) 
    {
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            throw new ForbiddenException("Forbidden Access : this customer account is not yours");
        }
        
        if(count($violations) > 0)
        {
            $message = "The JSON sent contains invalid Data: ";  
            foreach($violations as $violation)
            {
                $message .= sprintf('Field %s: %s ', $violation->getPropertyPath(), $violation->getMessage());
            }
            throw new ResourceNoValidateException($message);
        }

        $postDate = new \DateTime();
        $customer->setUpdatedAt($postDate);
        $user->setCustomer($customer);
        $user->setCreatedAt($postDate);        
        $this->em->persist($customer);
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
     * @OA\Parameter(
     *    name="customerId",
     *    in="path",
     *    description="ID of customer that needs to be used",
     *    required=true,
     *    @OA\Schema(
     *        type="integer",
     *        format="int64"
     *    )
     *  )
     * 
     * @OA\Parameter(
     *    name="userId",
     *    in="path",
     *    description="ID of user that you want deleting",
     *    required=true,
     *    @OA\Schema(
     *        type="integer",
     *        format="int64"
     *    )
     *  )
     * 
     * @OA\Response(
     *     response = 204,
     *     description = "User successfully deleted.",
     *     )
     * )
     * 
     * @OA\Response(
     *     response = 401,
     *     description = "You must use a valid token to complete this request"
     * )
     * 
     * @OA\Response(
     *     response = 403,
     *     description = "Forbidden access to this content"
     * )
     * 
     * @OA\Response(
     *     response = 404,
     *     description = "This resource doesn't exist !"
     * )
     * 
     * 
     */
    public function deleteAction(Customer $customer, User $user, Request $request)
    {
        if($this->tokenService->compareUsernameInTokenWithIdInUrl($request, $customer) === false)
        {
            throw new ForbiddenException("Forbidden Access : this customer account is not yours");
        }

        if($customer != $user->getCustomer())
        {
            throw new ForbiddenException("Forbidden Access : this user doesn't yours");
        }
        
        $customer->setUpdatedAt(new \DateTime());
        $this->em->persist($customer);
        $this->em->remove($user);
        $this->em->flush();
        return;
    }
}
