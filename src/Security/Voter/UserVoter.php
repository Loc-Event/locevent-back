<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends Voter
{
    private $security;
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['USER_EDIT', 'USER_VIEW', 'USER_DELETE'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        // $user contient les information de la personne connectée
        $user = $token->getUser();

        // Si on est pas connecté au site, on interdit automatiquement
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'USER_EDIT':
                // Si super Admin, alors on autorise l'accès à la page d'édition d'un utilisateur
                // if ($this->security->isGranted('ROLE_ADMIN')) {
                //     return true;
                // }

                // Si user est la cible alors on autorise l'accès à la page d'édition
                if ($user == $subject) {
                    return true;
                }

                // Un administrateur peut éditer le compte d'un simple utilisateur
                // On va vérifier que le compte à éditer à un role user

                $roles = $subject->getRoles();
                if (count($roles) == 1 && $roles[0] == 'ROLE_USER') {
                    return true;
                }

                break;
            case 'USER_VIEW':
                if ($user == $subject) {
                    return true;
                }
                break;
            case 'USER_DELETE':
                $roles = $subject->getRoles();
                
                if ($user == $subject) {
                    return true;
                }
                if (count($roles) == 1 && $roles[0] == 'ROLE_USER') {
                    return true;
                }
                break;
        }

        return false;
    }
}
