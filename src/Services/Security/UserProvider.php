<?php

namespace Assmat\Services\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Assmat\DataSource\Repositories;
use Assmat\DataSource\Domains;

class UserProvider implements UserProviderInterface
{
    private
        $contactRepositories;

    public function __construct(Repositories\Contact $contactRepositories)
    {
        $this->contactRepositories = $contactRepositories;
    }

    public function loadUserByUsername($username)
    {
        $contact = $this->contactRepositories->findFromEmail($username);

        if(!$contact instanceof Domains\Contact)
        {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return new ContactUser($contact);
    }

    public function refreshUser(UserInterface $user)
    {
        if(!$user instanceof UserInterface)
        {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'Assmat\Services\Security\ContactUser';
    }
}