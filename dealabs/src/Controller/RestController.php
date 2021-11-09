<?php

namespace App\Controller;

use App\Entity\Deals;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\FOSRestBundle;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;



use FOS\RestBundle\Controller\Annotations\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\ViewHandlerInterface;

class RestController extends AbstractFOSRestController
{

    /**
     * @Get(
     *     path = "/articles",
     *     name = "app_article_show"
     * )
     * @View
     */
    public function test()
    {
        $view = $this->view(array(), 200);

        return $this->handleView($view);
    }

}