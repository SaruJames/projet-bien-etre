<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\District;
use App\Entity\Internaut;
use App\Entity\Locality;
use App\Entity\Stage;
use App\Entity\User;
use App\Entity\Vendor;
use App\Entity\ZipCode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class VendorFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {

        $faker = Faker\Factory::create();

        $provinces = [
            [
                "Liste d'adresses"
            ],
            [
                ['Province','Zip', 'Localité'],
                ['Bruxelles',1000, 'Bruxelles'],
                ['Liège',4000, 'Liège'],
                ['Namur',5000, 'Namur'],
                ['Brabant Wallon',1490, 'Court-Saint-Etienne'],
                ['Brabant Flamand',1654, 'Huizingen'],
                ['Anvers',2390, 'Malle'],
                ['Limbourg',3730, 'Sint-Huibrechts-Hern'],
                ['Namur',5001, 'Belgrade'],
                ['Hainaut',6120, 'Ham-Sur-Heure'],
            ]
        ];
        $zipcodes = ['zips'];
        $localities = ['locs'];
        $vendorarray = [];

        for($h=1 ; $h<=9 ; $h++)
        {
            $zipcode = new ZipCode();
            $zipcode->setNumber($provinces[1][$h][1]);
            $manager->persist($zipcode);
            $zipcodes[] = $zipcode;

            $locality = new Locality();
            $locality->setName($provinces[1][$h][2]);
            $manager->persist($locality);
            $localities[] = $locality;
        };

        $highlight = true;
        $number = 1;

        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN'])
            ->setEmail('admin@admin.com')
            ->setSubDate(new \DateTime())
            ->setPassword($this->encoder->encodePassword($admin, 'admin'))
            ->setUsername('Admin');

        $manager->persist($admin);

        for($i=1 ; $i<=3 ; $i++){

            $category = new Category();
            $category->setName($faker->sentence(mt_rand(2, 3)))
                ->setDescription($faker->sentence)
                ->setHighlight($highlight)
                ->setValidity(true);
            $manager->persist($category);

            for($j=1 ; $j<=3 ; $j++) {

                $district = new District();
                $district->setName($provinces[1][$number][0]);
                $number++;

                $manager->persist($district);

                for($l=1 ; $l<=4 ; $l++) {

                    $rand= rand(1,9);

                    $vendors = new Vendor();
                    $vendors->setBanned(false)
                        ->setEmail($faker->email)
                        ->setSubDate(new \DateTime())
                        ->setPassword($this->encoder->encodePassword($vendors, 'vendor'))
                        ->setUsername($faker->userName)
                        ->setRoles(['ROLE_VENDOR'])
                    ;

                    $vendors->setDistrict($district)
                        ->setZipcode($zipcodes[$rand])
                        ->setLocality($localities[$rand])
                        ->setContactMail($faker->email)
                        ->setPhone($faker->phoneNumber)
                        ->setVat($faker->bankAccountNumber)
                        ->setWebsite($faker->url)
                        ->setDoorNumber($faker->numberBetween(1,100))
                        ->setStreet($faker->streetName)
                        ->addCategory($category)
                        ->setIsVisible(true)
                    ;

                    $vendorarray[] = $vendors;
                    $manager->persist($vendors);

                    $internaut = new Internaut();
                    $internaut->setUsername($faker->userName)
                        ->setPassword($this->encoder->encodePassword($internaut, 'internaut'))
                        ->setSubDate(new \DateTime())
                        ->setEmail($faker->email)
                        ->setBanned(false)
                    ;
                    $internaut->setNewsLetter(true);


                    $manager->persist($internaut);

                    $comment = new Comment();
                    $comment->setContent($faker->paragraph)
                        ->setInternaut($internaut)
                        ->setVendor($vendorarray[array_rand($vendorarray)]);

                    $manager->persist($comment);

                    for($m=1 ; $m<=rand(1,4) ; $m++) {
                        $stage = new Stage();
                        $stage->setVendor($vendors)
                            ->setName($faker->sentence)
                            ->setDescription($faker->paragraph)
                            ->setInfo($faker->sentence)
                            ->setPrice($faker->numberBetween(1, 999))
                            ->setDateBegin(new \DateTime())
                            ->setDateEnd(new \DateTime())
                            ->setShowDateBegin(new \DateTime())
                            ->setShowDateEnd(new \DateTime());

                        $manager->persist($stage);
                    }
                }
            }
            $highlight = false;
        }
        $manager->flush();
    }

}
