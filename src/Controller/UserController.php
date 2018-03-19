<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use FOS\UserBundle\Controller\SecurityController;

class UserController extends SecurityController
{
    protected function renderLogin(array $data)
    {
        //return $this->render('@FOSUser/Security/login.html.twig', $data);
        return $this->render('login.html.twig', $data);
    }
}
