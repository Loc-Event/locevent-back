<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;

class AuthenticationSuccessListener
{
    private $repository;

    // Construct the repo to get it into the method, neither the method wouldn't take it
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        // email of user who connect
        $email = $user->getUserIdentifier();

        // all data of this user
        $currentUser = $this->repository->findOneBy(['email' => $email]);

        // checks that current user is one of our user in the UserInterface with the same typing
        if (!$user instanceof UserInterface) {
            return;
        }

        // complementary data sent to the front in the same request
        // sends the id so the front can use this info for every request instead of email
        $data['userdata'] = array(
            'id' => $currentUser->getId(),
            'firstname' => $currentUser->getFirstname(),
            'lastname' => $currentUser->getLastname(),
        );

        $event->setData($data);
    }
}
