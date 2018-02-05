<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Figure;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $groupOne = (new Group())->setName('Les grabs');
        $groupTwo = (new Group())->setName('Les rotations');
        $groupThree = (new Group())->setName('Les flips');

        $manager->persist($groupOne);
        $manager->persist($groupTwo);
        $manager->persist($groupThree);

        $data = [
             [
             'name' => 'Mute',
             'description' => 'Saisie de la carre frontside de la planche entre les deux pieds avec la main avant',
             'group' => $groupOne,
             ],
             [
             'name' => 'Sad',
             'description' => 'Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant',
             'group' => $groupOne,
             ],
             [
             'name' => 'Indy',
             'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière',
             'group' => $groupOne,
             ],
             [
             'name' => 'Stalefish',
             'description' => 'Saisie de la carre backside de la planche entre les deux pieds avec la main arrière',
             'group' => $groupOne,
             ],
             [
             'name' => 'Tail grab',
             'description' => 'Saisie de la partie arrière de la planche, avec la main arrière',
             'group' => $groupOne,
             ],
             [
             'name' => 'Nose grab',
             'description' => 'Saisie de la partie avant de la planche, avec la main avant',
             'group' => $groupOne,
             ],
             [
             'name' => '180',
             'description' => "Un 180 désigne un demi-tour, soit 180 degrés d'angle",
             'group' => $groupTwo,
             ],
             [
             'name' => '360',
             'description' => 'Un 360, trois six représente un tour complet',
             'group' => $groupTwo,
             ],
             [
             'name' => 'Front flips',
             'description' => 'Rotation vertical en avant',
             'group' => $groupThree,
             ],
             [
             'name' => 'Back flips',
             'description' => 'Rotation verticale en arrière',
             'group' => $groupThree,
             ],
         ];

        foreach ($data as $value) {
            $figure = new Figure();
            $figure
                 ->setName($value['name'])
                 ->setDescription($value['description'])
                 ->setGroup($value['group']);

            $manager->persist($figure);
        }

        $manager->flush();
    }
}
