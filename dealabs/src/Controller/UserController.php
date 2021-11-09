<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\Avatar;
use App\Entity\Badge;
use App\Entity\Deals;
use App\Form\AccountDeleteType;
use App\Form\AccountDescriptionType;
use App\Form\AccountEmailType;
use App\Form\AccountImageType;
use App\Form\AccountLoginType;
use App\Form\AccountPasswordType;
use App\Entity\Alerte;
use App\Form\AlerteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private $manager;
    private $accountRepository;
    private $dealsRepository;
    private $badgeRepository;
    private $avatarRepository;
    private $alerteRepository;
    private $security;
    private $encoder;

    public function __construct(EntityManagerInterface $manager, Security $security, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->accountRepository = $manager->getRepository(Account::class);
        $this->dealsRepository = $manager->getRepository(Deals::class);
        $this->badgeRepository = $manager->getRepository(Badge::class);
        $this->avatarRepository = $manager->getRepository(Avatar::class);
        $this->alerteRepository = $manager->getRepository(Alerte::class);
        $this->security = $security;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/user", name="user")
     */
    public function userParams(): Response
    {
        $account = $this->accountRepository->findOneBy(['email' => $this->getUser()->getEmail()]);

        $userDeals = $this->dealsRepository->findBy(
            ['creator' => $account]
        );

        // Nombre de deals créés par l'utilisateur
        $nbDeals = $account->getDeals()->count();

        // Nombre de commentaires de l'utilisateur
        $nbComs = $account->getCommentaires()->count();

        // Deal de l'utilisateur le mieux noté
        $this->dealsRepository->calculateTemperatureDeal($userDeals);
        $hotMax = $this->dealsRepository->getHottestDeal($userDeals);

        // Moyenne des deals
        $lastYear = $this->dealsRepository->getLastYear();
        $moyenne = $this->dealsRepository->getMoyenneDeal($lastYear, $this->getUser());

        // pourcentage de deals hot
        $hotPercent = $this->dealsRepository->getHotPercent($userDeals);

        $badges = $this->badgeRepository->findBadgeByUser($account);
        return $this->render('user/stats.html.twig', [
            'nbDeals' => $nbDeals,
            'nbComs' => $nbComs,
            'badges' => $badges,
            'hotMax' => $hotMax,
            'moyenne' => $moyenne,
            'hotPercent' => $hotPercent
        ]);
    }

    /**
     * @Route("/saved", name="saved")
     * @param Request $request
     * @return Response
     */
    public function savedDeals(Request $request): Response
    {
        $account = $this->accountRepository->findOneBy(['email' => $this->getUser()->getEmail()]);
        if ($request->get("id") != null){
            $id = $request->get("id");
            $addedDeal = $this->dealsRepository->find($id);
            $account->addSavedDeal($addedDeal);
            $this->manager->persist($account);
            $this->manager->flush();
        }
        $deals = $account->getSavedDeals();
        return $this->render('deals/listing.html.twig', [
            'deals' => $deals,
            'tabTitle' => "Deals Sauvegardés",
            'pageTitle' => "Mes Deals Sauvegardés",
            'ext' => "user/menu-user.html.twig"
        ]);
    }

    /**
     * @Route("/parametres", name="parametres")
     */
    public function parametres(Request $request): Response
    {
        if($this->security->getUser() == null) {
            return $this->redirectToRoute('error');
        }

        $account = $this->security->getUser();
        $avatarTab = $this->avatarRepository->findByAccount($account);
        if($avatarTab != null) {
            $avatar = $avatarTab[0];
        } else {
            $avatar = new Avatar();
            $avatar->setAccount($account);
        }

        $imageForm = $this->createForm(AccountImageType::class, $avatar);
        $loginForm = $this->createForm(AccountLoginType::class, $account);
        $descriptionForm = $this->createForm(AccountDescriptionType::class, $account);
        $emailForm = $this->createForm(AccountEmailType::class, $account);
        $passwordForm = $this->createForm(AccountPasswordType::class, $account);
        $deleteForm = $this->createForm(AccountDeleteType::class, $account);

        $imageForm->handleRequest($request);
        $loginForm->handleRequest($request);
        $descriptionForm->handleRequest($request);
        $emailForm->handleRequest($request);
        $passwordForm->handleRequest($request);
        $deleteForm->handleRequest($request);

        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            //Si l'utilisateur possède déjà une image, on reset l'image
            if($avatarTab != null) {
                $avatar->setImage(new EmbeddedFile());
            }
            $avatar->setImageFile($avatar->getImageFile());
            $this->manager->persist($avatar);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'Votre avatar a été modifié avec succès'
            );
            $imageForm = $this->createForm(AccountImageType::class, $avatar);
        }
        if ($loginForm->isSubmitted() && $loginForm->isValid()) {
            $this->manager->persist($account);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'Votre pseudo a été modifié avec succès'
            );
        }
        if ($descriptionForm->isSubmitted() && $descriptionForm->isValid()) {
            $this->manager->persist($account);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'Votre description a été modifiée avec succès'
            );
        }
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $this->manager->persist($account);
            $this->manager->flush();
            $this->addFlash(
                'success',
                'Votre adresse e-mail a été modifiée avec succès'
            );
        }
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $firstPassword = $account->getNewPassword();
            $secondPassword = $account->getConfirmNewPassword();
            if(!$this->encoder->isPasswordValid($account, $account->getOldPassword())) {
                $this->addFlash(
                    'error',
                    'Votre mot de passe actuel est incorrect'
                );
            } else {
                if($firstPassword != $secondPassword) {
                    $this->addFlash(
                        'error',
                        'La confirmation de votre nouveau mot de passe ne corresond pas'
                    );
                } else {
                    $hash = $this->encoder->encodePassword($account, $firstPassword);
                    $account->setPassword($hash);
                    $this->manager->persist($account);
                    $this->manager->flush();
                    $this->addFlash(
                        'success',
                        'Votre mot de passe a été modifié avec succès'
                    );
                }
            }
        }
        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            if($this->encoder->isPasswordValid($account, $account->getOldPassword())) {
                $session = new Session();
                $session->invalidate();
                $this->manager->remove($account);
                $this->manager->flush();
                $this->addFlash(
                    'success',
                    'Votre compte a été supprimé avec succès'
                );
                return $this->redirectToRoute('login');
            } else {
                $this->addFlash(
                    'error',
                    'Votre mot de passe est invalide'
                );
            }
        }
        return $this->render('user/parametres.html.twig', [
            'imageForm' => $imageForm->createView(),
            'loginForm' => $loginForm->createView(),
            'descriptionForm' => $descriptionForm->createView(),
            'emailForm' => $emailForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'deleteForm' => $deleteForm->createView(),
            'avatar' => $avatar,
            'account' => $account
        ]);
    }

    /**
     * @Route("/created", name="created")
     * @return Response
     */
    public function createdDeals(): Response
    {
        $account = $this->accountRepository->findOneBy(['email' => $this->getUser()->getEmail()]);
        $deals = $this->dealsRepository->findBy(
            ['creator' => $account]
        );
        $deals = $this->dealsRepository->getDealsSortByDate($deals);
        return $this->render('deals/listing.html.twig', [
            'deals' => $deals,
            'tabTitle' => "Deals Sauvegardés",
            'pageTitle' => "Mes Deals Créés",
            'ext' => "user/menu-user.html.twig"
        ]);
    }

    /**
     * @Route("/new-alert", name="new-alert")
     * @param Request $request
     * @return Response
     */
    public function createAlerte(Request $request): Response
    {
        if($this->security->getUser() == null) {
            return $this->redirectToRoute('error');
        }
        // Sauvegarde nouvelle alerte
        $alerte = new Alerte();
        $form = $this->createForm(AlerteType::class, $alerte);
        $form->handleRequest($request);
        if ($form->isSubmitted()){
            $alerte->setCreator($this->getUser());
            $this->manager->persist($alerte);
            $this->manager->flush();
        }

        $alertes = $this->alerteRepository->findBy([
            'creator' => $this->getUser()
        ]);

        return $this->render('user/new-alert.html.twig', [
            'form' => $form->createView(),
            'alertes' => $alertes
        ]);
    }

    /**
     * @Route("/del-alert/{id}", name="del-alerte")
     * @param Request $request
     * @return Response
     */
    public function deleteAlerte(Request $request): Response
    {
        $id = $request->get('id');
        $alerte = $this->alerteRepository->find($id);
        $this->manager->remove($alerte);
        $this->manager->flush();
        return $this->redirectToRoute("new-alert");
    }

    /**
     * @Route("/fil-alert", name="fil-alert")
     * @param Request $request
     * @return Response
     */
    public function filAlerte(Request $request): Response
    {
        $deals = array();
        $alertes = $this->alerteRepository->findBy(
            ['creator' => $this->getUser()]
        );
        foreach ($alertes as $alerte){
            $deals = $deals + $this->dealsRepository->findByKeyWord($alerte->getMotscles());
        }
        return $this->render('user/fil-alert.html.twig', [
            'deals' => $deals,
            'tabTitle' => "Fil d'alertes",
            'pageTitle' => "Mon fil d'alertes",
            'ext' => "user/empty-base.html.twig"
        ]);
    }
}
