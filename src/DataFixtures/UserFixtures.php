<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{

    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder){

        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $NbrUsers  = 10;
        for ($i=0; $i<10; $i++)
        {
            $user = new User();
            $user->setUsername("Username_".$i);
            $user->setPassword($this->userPasswordEncoder->encodePassword($user,"Password_".$i));
            $manager->persist($user);
        }
        $manager->flush();
    }
}
