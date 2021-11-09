<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Badge;
use App\Entity\Deals;
use App\Entity\Groupe;
use App\Entity\Partenaire;
use App\Entity\Vote;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Liior\Faker\Prices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new PicsumPhotosProvider($faker));
        $faker->addProvider(new Prices($faker));

        //Comptes
        $account = new Account;
        $account->setEmail("antoine@gmail.com")
            ->setPassword($this->encoder->encodePassword($account, '12345678'))
            ->setLogin('Antoine')
            ->setRoles(['ROLE_ADMIN']);
        $manager->persist($account);

        $account2 = new Account;
        $account2->setEmail("alexandre.farjas@etu.uca.fr")
            ->setPassword($this->encoder->encodePassword($account2, '12041204'))
            ->setLogin('alfarjas');
        $manager->persist($account2);

        //Partenaires
        $partenaires = array();
        for ($d = 0; $d<10; $d++){
            $partenaire = new Partenaire;
            $partenaire->setNom($faker->company)
                ->setUrl($faker->url);
            $manager->persist($partenaire);
            $partenaires[$d] = $partenaire;
        }

        //Groupes
        $groupes = array();
        $groupeNames = array("High-Tech", "Epicerie & courses", "Mode & accessoires", "Sports & plein air", "Voyages", "Culture & divertissement", "Santé & Cosmétique", "Famille & enfants",
            "Maison & Habitat", "Jardin & bricolage", "Auto-Moto", "Finances & Assurances", "Forfaits mobiles et internet", "Services", "Gratuit");
        foreach($groupeNames as $name) {
            $groupe = new Groupe();
            $groupe->setNom($name);
            $manager->persist($groupe);
            array_push($groupes, $groupe);
        }

        //Badges
        $badges = array();
        $badgeNames = array("Surveillant", "Cobaye", "Rapport de stage");
        foreach($badgeNames as $name) {
            $badge = new Badge();
            $badge->setName($name);
            $manager->persist($badge);
            array_push($badges, $badge);
        }

        $creationDate = new \DateTime();

        $expirationDate = new \DateTime();
        $expirationDate->setTimestamp(time()+2629800);

        $votes = array();

        for ($d = 0; $d<10; $d++){

            //Vote et Deals
            $bonplan = new Deals;

            $price = $faker->price(40,200);
            $bonplan->setShipping($faker->price(2,8))
                ->setTitle($faker->sentence)
                ->setPartenaire($partenaires[mt_rand(0, sizeof($partenaires) - 1)])
                ->setDescription($faker->sentence())
                ->setCreationDate($creationDate->setTimestamp(time()-mt_rand(0,2629800)))
                ->setCreator($account)
                ->setDiscountPrice($faker->price($price-$price/4,$price))
                ->setNormalPrice($price)
                ->setLink($faker->url)
                ->setExpirationDate($expirationDate)
                ->setType('deal')
                ->addGroupe($groupes[mt_rand(0, sizeof($groupes) - 1)]);

            if($d == 0){
                for ($i = 0; $i<150; $i++){
                    $vote = new Vote;
                    $vote->setAccount($account)
                        ->setDeals($bonplan)
                        ->setIsPositive(true);
                    $manager->persist($vote);
                    $bonplan->addVote($vote);
                }
            }
            else{
                for ($i = 0; $i<mt_rand(0,10); $i++){
                    $vote = new Vote;
                    $vote->setAccount($account2)
                        ->setDeals($bonplan)
                        ->setIsPositive(true);
                    $manager->persist($vote);
                    $bonplan->addVote($vote);
                }
            }
            $manager->persist($bonplan);


            $code = new Deals;
            $code->setShipping($faker->price(2,8))
                ->setTitle($faker->sentence)
                ->setPartenaire($partenaires[mt_rand(0, sizeof($partenaires) - 1)])
                ->setDiscountPrice($faker->price(1,20))
                ->setDescription($faker->sentence())
                ->setCreationDate($creationDate->setTimestamp(time()-mt_rand(0,2629800)))
                ->setCreator($account)
                ->setWebsite($faker->url)
                ->setType('code')
                ->setPromoCode("GOOGLE10")
                ->setExpirationDate($expirationDate)
                ->addGroupe($groupes[mt_rand(0, sizeof($groupes) - 1)]);
            $manager->persist($code);

            if($d >= 0 && $d < 2){
                for ($i = 0; $i < mt_rand(100, 110); $i++) {
                    $vote = new Vote;
                    $vote->setAccount($account2)
                        ->setDeals($code)
                        ->setIsPositive(true);
                    $manager->persist($vote);
                    $code->addVote($vote);
                }
            }
            else {
                for ($i = 0; $i < mt_rand(0, 10); $i++) {
                    $vote = new Vote;
                    $vote->setAccount($account2)
                        ->setDeals($code)
                        ->setIsPositive(true);
                    $manager->persist($vote);
                    $code->addVote($vote);
                }
            }
            $manager->persist($code);
        }

        $manager->flush();
    }
}
