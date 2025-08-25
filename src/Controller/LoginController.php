<?php

namespace App\Controller;

use App\Enum\RoleEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/se-connecter', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted(RoleEnum::ROLE_ADMIN)) {
            return $this->redirectToRoute('bo_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/redirection-connexion', name: 'app_login_redirect')]
    public function loginRedirect(): Response
    {
        if (null !== $this->getUser() && in_array(RoleEnum::ROLE_ADMIN, $this->getUser()->getRoles())) {
            return $this->redirectToRoute('bo_dashboard');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route(path: '/se-deconnecter', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
