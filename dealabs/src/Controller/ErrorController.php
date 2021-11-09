<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ErrorController extends AbstractController
{
    /**
     * @Route("/not/connected/error", name="error")
     */
    public function notConnectedError(): Response
    {
        return $this->render('errors/notconnected.html.twig', [
            'controller_name' => 'ErrorController',
        ]);
    }
}
