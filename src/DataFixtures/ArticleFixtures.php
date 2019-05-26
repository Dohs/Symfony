<?php


namespace App\DataFixtures;


use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\service\Slugify;
class ArticleFixtures extends Fixture implements DependentFixtureInterface
{

    const CATEGORIES = [
        'javascript',
        'php',
        'ruby',
    ];


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 1; $i <= 50; $i++) {
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->title()));
            $slugify= new Slugify();
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);
            $article->setContent(mb_strtolower($faker->sentence()));
            $manager->persist($article);
            $article->setCategory($this->getReference('categorie_'.rand(0,2)));
            $manager->flush();
        }
    }
    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}