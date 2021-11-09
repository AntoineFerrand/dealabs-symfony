<?php

namespace App\Controller;

use App\Entity\Deals;
use App\Repository\DealsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private $dealsRepository;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->dealsRepository = $manager->getRepository(Deals::class);
    }

    /**
     * @Route("/search", name="search")
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        $keyword = $request->get("q");
        $deals = $this->dealsRepository->findByKeyWord($keyword);
        return $this->render('deals/listing.html.twig', [
            'deals' => $deals,
            'tabTitle' => "Recherche un deal",
            'pageTitle' => "Résultat de votre recherche (".sizeof($deals)." élément(s))",
            'ext' => 'base.html.twig'
        ]);
    }
}
