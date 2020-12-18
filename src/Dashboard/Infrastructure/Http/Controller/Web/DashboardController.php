<?php

namespace Dashboard\Infrastructure\Http\Controller\Web;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class DashboardController extends AbstractController
{
    private Security $security;

    /**
     * DashboardController constructor.
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="dashboard.main")
     */
    public function dashboardMain(Request $request): Response
    {
        $user = $this->security->getUser();

        return $this->render('@Dashboard/dashboard.html.twig', []);
    }
}
