<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Humeur;
use DateTimeImmutable;
use App\Entity\MyDayPost;
use App\Entity\HumeurType;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        //On crée un User qui sera le compte de test de l'appli
        $user = new User();
        $user->setEmail('comptedetest@test.fr');
        $hashedPassword = $this->passwordEncoder->hashPassword($user,'comptedetest');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        //On crée 25 posts
        for ($i = 0; $i < 25; $i++)
        {
            $post = new MyDayPost();
            $post->setCreatedAt(new DateTimeImmutable('now'));
            $post->setDescription($faker->sentence($nbWords = 45, $variableNbWords = true));
            $post->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));

            $slugify = new Slugify();
            $post->setSlug($slugify->slugify($post->getTitle()));
            $post->setPrivate($faker->boolean($chanceOfGettingTrue = 50));
            $post->setUser($user);

            $manager->persist($post);
        }

        //On crée les 5 types d'humeur
        $angry = new HumeurType();
        $angry->setImage('angry.png');
        $angry->setLibelle('angry');
        $angry->setHapinessLevel(1);

        $upset = new HumeurType();
        $upset->setImage('upset.png');
        $upset->setLibelle('upset');
        $upset->setHapinessLevel(2);

        $neutral = new HumeurType();
        $neutral->setImage('neutral.png');
        $neutral->setLibelle('neutral');
        $neutral->setHapinessLevel(3);
        
        $happy = new HumeurType();
        $happy->setImage('happy.png');
        $happy->setLibelle('happy');
        $happy->setHapinessLevel(4);

        $excited = new HumeurType();
        $excited->setImage('excited.png');
        $excited->setLibelle('excited');
        $excited->setHapinessLevel(5);

        $manager->persist($angry);
        $manager->persist($upset);
        $manager->persist($neutral);
        $manager->persist($happy);
        $manager->persist($excited);

        //On crée 100 Humeurs (points sur le graphique)
        for ($i = 0; $i < 100; $i++)
        {
            $humeur = new Humeur();
            $humeur->setUser($user);
            $humeur->setDescription($faker->sentence($nbWords = 20, $variableNbWords = true));
            $humeur->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null)));
            $humeur->setHumeurType($faker->randomElement([$angry,$upset,$neutral,$happy,$excited]));

            $manager->persist($humeur);
        }

        $manager->flush();
    }
}
