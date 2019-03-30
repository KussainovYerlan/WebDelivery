<?php

namespace App\Security\Voter;

use App\Entity\DeliveryOrder;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['add', 'view', 'submit'])
            && $subject instanceof DeliveryOrder;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'add':
                return $this->canAdd($user, $subject);
                break;
            case 'view':
                return $this->canView($user, $subject);
                break;
            case 'submit':
        }

        return false;
    }

    protected function canAdd(User $user, DeliveryOrder $order)
    {
        foreach ($user->getRoles() as $item) {
            if ($item == "ROLE_USER") {
                $products = $order->getProducts();
                $seller = $order->getSeller();
                foreach ($products as $product)
                {
                    if ($product->getSeller() !== $seller)
                    {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    protected function canView(User $user, DeliveryOrder $order)
    {
        foreach ($user->getRoles() as $item) {

            if (($item == "ROLE_SELLER_MAIN") || ($item === "ROLE_SELLER_MANAGER")) {
                if ($order->getSeller() === $user->getSeller())
                {
                    return true;
                }
            }
        }
        return false;
    }

    protected function canSubmit(User $user, DeliveryOrder $order)
    {
        foreach ($user->getRoles() as $item) {
            if (($item == "ROLE_SELLER_MAIN") || ($item == "ROLE_SELLER_MANAGER")) {
                if ($order->getSeller() === $user->getSeller())
                {
                    return true;
                }
            }
        }
        return false;
    }
}
