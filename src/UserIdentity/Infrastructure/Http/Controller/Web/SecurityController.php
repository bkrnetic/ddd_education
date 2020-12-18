<?php

namespace UserIdentity\Infrastructure\Http\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/login", name="userIdentity.login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils): Response
    {
        if ($this->security->getUser() !== null) {
            return $this->redirect($this->generateUrl('dashboard.main'));
        }

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('@UserIdentity/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="userIdentity.logout")
     */
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key in firewall.
    }
}
