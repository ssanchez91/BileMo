<?php

namespace App\Service;

use App\Entity\Customer;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * TokenService class
 */
class TokenService
{
    /**
     * jwt variable
     *
     * @var [type]
     */
    protected $jwt;
    
    public function __construct(JWTEncoderInterface $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * Allow to compare customer in Token with customer in url.
     *
     * @param Request $request
     * @return boolean
     */
    public function compareUsernameInTokenWithIdInUrl(Request $request, Customer $customer): bool
    {
        /*Token used in header request*/
        $token = substr($request->headers->get('Authorization'),7);
        
        /*Username (email in our case) used in the payload of the Token*/
        $username = $this->jwt->decode($token)['username'];

        // dd($username, $customer->getEmail());

        if($username != $customer->getEmail())
        {
            return false;
        }

        return true;
    }
}