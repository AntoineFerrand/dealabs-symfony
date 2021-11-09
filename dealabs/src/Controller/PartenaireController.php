<?php

namespace App\Controller;

use App\Entity\Partenaire;
use App\Form\PartenaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;

class PartenaireController extends AbstractController
{
    private $security;
    private $manager;

    public function __construct(Security $security, EntityManagerInterface $manager)
    {
        $this->security = $security;
        $this->manager = $manager;
    }

    /**
     * @Route("/create/partenaire", name="createPartenaire")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function create(Request $request): Response
    {
        $partenaire = new Partenaire();
        $form = $this->createForm(PartenaireType::class, $partenaire);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            if($this->security->getUser() == null) {
                return $this->redirectToRoute('error');
            }
            $this->manager->persist($partenaire);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'Votre partenaire a bien été ajouté'
            );
            unset($partenaire);
            unset($form);
            $partenaire = new Partenaire();
            $form = $this->createForm(PartenaireType::class, $partenaire);
        }

        return $this->render('partenaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
