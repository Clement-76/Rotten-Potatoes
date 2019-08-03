<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Movie;
use App\Entity\People;
use App\Entity\Rating;
use App\Entity\User;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture {

    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) {

        $faker = Factory::create('fr_FR');
        $slugify = new Slugify();
        $i = 0;

        $users = [];

        for ($a = 0; $a < 25; $a++) {
            $user = new User();

            $user->setEmail("user$a@gmail.com")
                ->setAvatar("https://i.pravatar.cc/100?img=$i")
                ->setName($faker->userName)
                ->setPassword($this->encoder->encodePassword($user, 'Azeaze76'));

            $i++;

            if ($faker->boolean) {
                if ($faker->boolean) {
                    $user->setRoles(['ROLE_MODERATOR']);
                } else {
                    $user->setRoles(['ROLE_ADMIN']);
                }
            }

            $users[] = $user;
            $manager->persist($user);
        }

        $categories = [];

        for ($b = 0; $b < 10; $b++) {
            $category = new Category();

            $category->setTitle($faker->sentence(2))
                ->setSlug($slugify->slugify($category->getTitle()));

            $categories[] = $category;
            $manager->persist($category);
        }

        $manyPeople = [];

        for ($c = 0; $c < 10; $c++) {
            $people = new People();

            $people->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setDescription($faker->paragraph)
                ->setPicture("https://i.pravatar.cc/180?img=$i")
                ->setBirthday($faker->dateTimeThisCentury);

            $i++;

            $fullName = $people->getFirstName() . ' ' . $people->getLastName();
            $people->setSlug($slugify->slugify($fullName));

            $manyPeople[] = $people;
            $manager->persist($people);
        }

        for ($d = 0; $d < 12; $d++) {
            $movie = new Movie();
            $id = 1000 + $d;

            $movie->setTitle($faker->sentence)
                ->setSlug($slugify->slugify($movie->getTitle()))
                ->setDirector($faker->randomElement($manyPeople))
                ->setPoster('https://picsum.photos/id/' . $id . '/300/390')
//                ->setPoster("https://picsum.photos/200/300?random=$d")
                ->setReleasedAt($faker->dateTimeBetween('-5 years'))
                ->setSynopsis($faker->realText(300));

            $randomActors = $faker->randomElements($manyPeople, mt_rand(1, 5));

            foreach ($randomActors as $actor) {
                $movie->addActor($actor);
            }

            $randomCategories = $faker->randomElements($categories, mt_rand(1, 3));

            foreach ($randomCategories as $category) {
                $movie->addCategory($category);
            }

            for ($e = 0; $e < mt_rand(0, 10); $e++) {
                $rating = new Rating();

                $rating->setAuthor($faker->randomElement($users))
                    ->setCreatedAt($faker->dateTimeBetween('-5 years'))
                    ->setNotation(mt_rand(0, 5))
                    ->setMovie($movie)
                    ->setComment($faker->sentence(mt_rand(80, 150)));

                $movie->addRating($rating);

                $manager->persist($rating);
            }

            $manager->persist($movie);
        }

        $manager->flush();
    }
}
