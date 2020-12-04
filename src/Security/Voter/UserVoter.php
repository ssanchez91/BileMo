<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['SHOW', 'DELETE'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $customer = $token->getUser();
        
        // if the user is anonymous, do not grant access
        if (!$customer instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'SHOW':
                return $subject->getCustomer()->getId() == $customer->getId();
                break;
            case 'DELETE':
                return $subject->getCustomer()->getId() == $customer->getId();
                break;
        }

        return false;
    }
}
