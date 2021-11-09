<?php

namespace App\Controller;

use App\Entity\Deals;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $manager;
    private $dealsRepository;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->dealsRepository = $this->manager->getRepository(Deals::class);
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function aLaUne(): Response
    {
        $deals = $this->dealsRepository->calculateTemperatureDeal($this->dealsRepository->findAll());
        return $this->render('deals/listing.html.twig', [
            'deals' => $deals,
            'tabTitle' => "Deals à la une",
            'pageTitle' => "Listing des deals à la une",
            'ext' => "base.html.twig"
        ]);
    }

    /**
     * @Route("/hot", name="hot")
     * @return Response
     */
    public function hot(): Response
    {
        $deals = $this->dealsRepository->findAllGreaterthan100();
        return $this->render('deals/listing.html.twig', [
            'deals' => $deals,
            'tabTitle' => "Les deals les plus chauds",
            'pageTitle' => "Listing des deals les plus \"hot\" !",
            'ext' => "base.html.twig"
        ]);
    }
}
