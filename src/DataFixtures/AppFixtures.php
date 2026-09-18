<?php

namespace App\DataFixtures;

use App\Entity\Utilisateur;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\Allergene;
use App\Entity\Menu;
use App\Entity\Plat;
use App\Entity\Regime;
use App\Entity\Theme;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\ImageMenu;

class AppFixtures extends Fixture
{
    public function __construct(
    private UserPasswordHasherInterface $passwordHasher
) {
}
    public function load(ObjectManager $manager): void
    {
        // Administrateur initial : José
$jose = new Utilisateur();
$jose->setNom('José');
$jose->setPrenom('José');
$jose->setTelephone('0600000000');
$jose->setAdresse('Adresse à compléter');
$jose->setEmail('jose@vite-et-gourmand.fr');
$jose->setRoles(['ROLE_ADMINISTRATEUR']);

$jose->setPassword(
    $this->passwordHasher->hashPassword($jose, 'JoseAdmin123!')
);

$manager->persist($jose);
        // Thèmes
        $themeClassique = new Theme();
        $themeClassique->setNom('Classique');
        $manager->persist($themeClassique);

        $themeNoel = new Theme();
        $themeNoel->setNom('Noël');
        $manager->persist($themeNoel);

        $themePaques = new Theme();
        $themePaques->setNom('Pâques');
        $manager->persist($themePaques);

        $themeEvenement = new Theme();
        $themeEvenement->setNom('Événement');
        $manager->persist($themeEvenement);

        // Régimes
        $regimeClassique = new Regime();
        $regimeClassique->setNom('Classique');
        $manager->persist($regimeClassique);

        $regimeVegetarien = new Regime();
        $regimeVegetarien->setNom('Végétarien');
        $manager->persist($regimeVegetarien);

        $regimeVegan = new Regime();
        $regimeVegan->setNom('Vegan');
        $manager->persist($regimeVegan);

        // Allergènes
        $gluten = new Allergene();
        $gluten->setNom('Gluten');
        $manager->persist($gluten);

        $lait = new Allergene();
        $lait->setNom('Lait');
        $manager->persist($lait);

        $oeufs = new Allergene();
        $oeufs->setNom('Œufs');
        $manager->persist($oeufs);

        $fruitsCoque = new Allergene();
        $fruitsCoque->setNom('Fruits à coque');
        $manager->persist($fruitsCoque);
        // Plats
$salade = new Plat();
$salade->setNom('Salade gourmande');
$salade->setDescription('Salade fraîche aux légumes de saison.');
$salade->setType('entree');
$manager->persist($salade);

$saumon = new Plat();
$saumon->setNom('Saumon rôti');
$saumon->setDescription('Saumon rôti accompagné de légumes.');
$saumon->setType('plat');
$manager->persist($saumon);

$gratin = new Plat();
$gratin->setNom('Gratin de légumes');
$gratin->setDescription('Gratin de légumes de saison.');
$gratin->setType('plat');
$gratin->addAllergene($lait);
$manager->persist($gratin);

$fondant = new Plat();
$fondant->setNom('Fondant au chocolat');
$fondant->setDescription('Fondant au chocolat maison.');
$fondant->setType('dessert');
$fondant->addAllergene($gluten);
$fondant->addAllergene($lait);
$fondant->addAllergene($oeufs);
$manager->persist($fondant);

$saladeFruits = new Plat();
$saladeFruits->setNom('Salade de fruits');
$saladeFruits->setDescription('Fruits frais de saison.');
$saladeFruits->setType('dessert');
$manager->persist($saladeFruits);
// Menus
$menuClassique = new Menu();
$menuClassique->setTitre('Menu Gourmand');
$menuClassique->setDescription('Un menu gourmand et traditionnel.');
$menuClassique->setNombreMinimumPersonnes(2);
$menuClassique->setPrixMinimum('45.00');
$menuClassique->setConditions('Commande au minimum 48 heures à l’avance.');
$menuClassique->setStock(10);
$menuClassique->setTheme($themeClassique);
$menuClassique->setRegime($regimeClassique);
$menuClassique->addPlat($salade);
$menuClassique->addPlat($saumon);
$menuClassique->addPlat($fondant);
$manager->persist($menuClassique);

$menuVegetarien = new Menu();
$menuVegetarien->setTitre('Menu Végétarien');
$menuVegetarien->setDescription('Un menu sans viande à base de légumes de saison.');
$menuVegetarien->setNombreMinimumPersonnes(2);
$menuVegetarien->setPrixMinimum('35.00');
$menuVegetarien->setConditions('Commande au minimum 48 heures à l’avance.');
$menuVegetarien->setStock(8);
$menuVegetarien->setTheme($themeClassique);
$menuVegetarien->setRegime($regimeVegetarien);
$menuVegetarien->addPlat($salade);
$menuVegetarien->addPlat($gratin);
$menuVegetarien->addPlat($saladeFruits);
$manager->persist($menuVegetarien);

// Images du Menu Gourmand
$imageGourmand = new ImageMenu();
$imageGourmand->setNomFichier('menu-gourmand.png');
$imageGourmand->setMenu($menuClassique);
$manager->persist($imageGourmand);

$imagePlatGourmand = new ImageMenu();
$imagePlatGourmand->setNomFichier('plat-gourmand.png');
$imagePlatGourmand->setMenu($menuClassique);
$manager->persist($imagePlatGourmand);

$imageDessert = new ImageMenu();
$imageDessert->setNomFichier('dessert-chocolat.png');
$imageDessert->setMenu($menuClassique);
$manager->persist($imageDessert);

// Image du Menu Végétarien
$imageVegetarien = new ImageMenu();
$imageVegetarien->setNomFichier('menu-vegetarien.png');
$imageVegetarien->setMenu($menuVegetarien);
$manager->persist($imageVegetarien);

        $manager->flush();
    }
}