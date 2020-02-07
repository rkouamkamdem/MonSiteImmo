<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Property;
use Faker\Factory as FAKER;

class PropertyFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //$faker = new Factory();
        //$faker::create('fr_FR');
        $faker = FAKER::create('fr_FR');

        for ( $i=0; $i < 100; $i++) {
            $property = new Property();

            //Ici on génère un titre aléatoire avec Faker, un titre qui aura 3 mots
            $property->setTitle($faker->words(3,true) );
            //Ici notre description sera 1 phrase qui a 7 mots
            $property->setDescription($faker->sentences(3,true));
            //Ici Faker nous donne un nombre compris entre 20 et 350 pour la surface
            $property->setSurface($faker->numberBetween(20,350));
            $property->setRooms($faker->numberBetween(2,10));
            $property->setBedrooms($faker->numberBetween(1,9));
            $property->setFloor($faker->numberBetween(0,15));
            $property->setPrice($faker->numberBetween(100000,1000000));
            $property->setHeat(0, count(Property::HEAT)-1);
            $property->setCity($faker->city);
            $property->setAddress($faker->address);
            $property->setPostalCode($faker->postcode);
            $property->setSold(false);
            $manager->persist($property);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
