<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Figure;
use App\Entity\User;
use App\Entity\Picture;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $kernel;

    public function __construct(UserPasswordEncoderInterface $encoder, KernelInterface $kernel)
    {
        $this->encoder = $encoder;
        $this->kernel = $kernel;
    }

    public function load(ObjectManager $manager)
    {
        $groupOne = (new Group())->setName('Les grabs');
        $groupTwo = (new Group())->setName('Les rotations');
        $groupThree = (new Group())->setName('Les flips');

        $manager->persist($groupOne);
        $manager->persist($groupTwo);
        $manager->persist($groupThree);

        if ($this->kernel->getEnvironment() == 'test') {
            $perso = (new User())
                    ->setEmail('email@email.com')
                    ->setUsername('perso')
                    ->setPassword('password')
                    ->setPhoto((new Picture())->setExtension('png'))
                    ->setIsActive(true)
                    ->setRoles(array('ROLE_USER'));

            $persoConfirm = (new User())
                    ->setEmail('persoemail@email.com')
                    ->setUsername('persoconfirm')
                    ->setPassword('password')
                    ->setPhoto((new Picture())->setExtension('png'))
                    ->setIsActive()
                    ->setValidationToken('my_test_validation_token')
                    ->setRoles(array('ROLE_USER'));

            $persoReinitPass = (new User())
                    ->setEmail('passwordemail@email.com')
                    ->setUsername('persoreinit')
                    ->setPassword('password')
                    ->setPhoto((new Picture())->setExtension('png'))
                    ->setIsActive(true)
                    ->setResetToken('my_test_reset_token')
                    ->setRoles(array('ROLE_USER'));
        

            $password = $this->encoder->encodePassword($perso, $perso->getPassword());

            $perso->setPassword($password);

            $manager->persist($perso);
            $manager->persist($persoConfirm);
            $manager->persist($persoReinitPass);
        }

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
            $picture1 = new Picture();
            $picture1->setExtension('jpg');

            $picture2 = new Picture();
            $picture2->setExtension('png');
            
            $picture3 = new Picture();
            $picture3->setExtension('jpg');

            $figure = new Figure();
            $figure->setName($value['name'])
                 ->setDescription($value['description'])
                 ->setGroup($value['group']);
            
            if ($this->kernel->getEnvironment() == 'test') {
                $figure->addImage($picture1)
                     ->addImage($picture2)
                     ->addImage($picture3)
                     ->addVideo((new Video())->setUrl('https://www.youtube.com/watch?v=k-ImCpNqbJw'))
                     ->addVideo((new Video())->setUrl('https://www.youtube.com/watch?v=k-ImCpNqbJw'));
            }


            $manager->persist($figure);
        }

        $manager->flush();
    }
}
