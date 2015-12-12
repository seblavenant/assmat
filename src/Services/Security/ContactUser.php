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
        $accountNonLocked,
        $roles,
        $salt;

    public function __construct(Domains\Contact $contact, $salt)
    {
        if($contact->getEmail() === null)
        {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->contact = $contact;
        $this->roles = array('ROLE_ADMIN');
        $this->salt = $salt;

        $this->enabled = true;
        $this->accountNonExpired = true;
        $this->credentialsNonExpired = true;
        $this->accountNonLocked = true;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function addRoles($role)
    {
        $this->roles[] = $role;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->contact->getPassword();
    }

    public function getSalt()
    {
        return $this->salt;
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