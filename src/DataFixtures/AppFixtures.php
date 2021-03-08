<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private $userPasswordEncode;
    public function __construct(UserPasswordEncoderInterface $userPasswordEncode){
        $this->userPasswordEncode = $userPasswordEncode ;
    }
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        //create 2 admin and 10 users

        $users = $this->createUser(10);
        $userAdmins = $this->createUser(2,true);

        $users = array_merge($users,$userAdmins);

    

        $mng = array_reduce($users,function($manager,$user)  {
           $manager->persist($user);
           return $manager;
        },$manager);

        $mng->flush();
    }


    public function createUser(int $nb , $admin = false) : array {
        $userArray = [];

        for($i=0;$i<$nb;$i++){
            $user = new User();
            $faker = Factory::create();

            if($i%2 == 0 ){
                $user->setName($faker->name($gener='female'));
                $user->setUserName($faker->userName);
                $user->setGender('female');
            }
            else{
                if(!$admin)
                 $user->setEmail($faker->safeEmail);
                $user->setName($faker->name($gener='male'));
                $user->setGender('male');
            }
            $password = "1234" ;
            $user->setPassword($this->userPasswordEncode->encodePassword($user, $password));

            if($admin){
                $user->setRoles(['ROLE_ADMIN']);
                $user->setEmail("admin".$i."@gmail.com");
            }
                
            $userArray[] = $user ;
        }

        return $userArray;
    }
}
