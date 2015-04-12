<?php

namespace Assmat\Services\Security;

use Assmat\DataSource\Domains;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

class ContactUser implements AdvancedUserInterface
{
    private
        $contact,
        $enabled,
        $accountNonExpired,
        $credentialsNonExpired,
        $accountNonLocked;

    public function __construct(Domains\Contact $contact, $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        if($contact->getEmail() === null)
        {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->contact = $contact;
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function getRoles()
    {
        return array('ROLE_ADMIN');
    }

    public function getPassword()
    {
        return $this->contact->getPassword();
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->contact->getEmail();
    }

    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function eraseCredentials()
    {
    }

}