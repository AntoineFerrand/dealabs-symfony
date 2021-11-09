<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    private $manager;
    private $encoder;
    private $authenticationUtils;

    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder, AuthenticationUtils $authenticationUtils)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
        $this->authenticationUtils = $authenticationUtils;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registration(Request $request): Response
    {
        $account = new Account();
        $form = $this->createForm(RegistrationType::class, $account);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $this->encoder->encodePassword($account, $account->getPassword());
            $account->setPassword($hash);
            $this->manager->persist($account);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'Votre compte a été enregistré, vous pouvez vous connecter'
            );
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(): Response
    {
        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
