<?php


namespace App\Controller;

use App\Form\CategoryType;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use App\Form\ArticleSearchType;
use Doctrine\Common\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
//    /**
//     * Getting a article with a formatted slug for title
//     *
//     * @param string $slug The slugger
//     *
//     * @Route("/blog/show/{slug<^[a-z0-9-]+$>}",
//     *     defaults={"slug" = null},
//     *     name="blog_show")
//     * @return Response A response instance
//     */
//    public function show(string $slug): Response
//    {
//        if (!$slug) {
//            throw $this->createNotFoundException('No slug has been sent to find an article in article\'s table.');
//        }
//
//        $slug = preg_replace(
//            '/-/',
//            ' ', ucwords(trim(strip_tags($slug)), "-")
//        );
//
//        $article = $this->getDoctrine()
//            ->getRepository(Article::class)
//            ->findOneBy(['title' => mb_strtolower($slug)]);
//
//        if (!$article) {
//            throw $this->createNotFoundException(
//                'No article with ' . $slug . ' title, found in article\'s table.'
//            );
//        }
//
//        return $this->render(
//            'blog/show.html.twig',
//            [
//                'article' => $article,
//                'slug' => $slug,
//            ]
//        );
//    }

    /**
     * Show all row from article's entity
     * @IsGranted("ROLE_USER")
     * @Route("/blog", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }
        $form = $this->createForm(
            ArticleSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );

        return $this->render(
            'blog/index.html.twig',
            ['articles' => $articles,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/blog/category/{name}", name="show_category")
     * @ParamConverter("category", class="App\Entity\Category")
     */
    public function showByCategory(Category $category): Response
    {
//        $category = new Category();
//        $form = $this->createForm(CategoryType::class,
//            $category);

        return $this->render(
            'blog/category.html.twig',
            [
                'category' => $category,
//                'form' => $form->createView(),
            ]
        );

    }

    /**
     * @Route("/category",name="category_form")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addCategory(Request $request, ObjectManager $manager)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category)
            ->add('name');
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('show_category', ['name' => $category->getName()]);
        }
        return $this->render(
            'blog/categoryForm.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/blog/tag/{name}", name="show_tag")
     * @ParamConverter("tag", class="App\Entity\Tag")
     */
    public function showByTag(Tag $tag): Response
    {
        return $this->render(
            'blog/showByTag.html.twig',
            [
                'tag' => $tag
            ]
        );
    }

    /**
     * @Route("/blog/article/{article_id}", name="show_article")
     * @ParamConverter("article", class="App\Entity\Article", options={"id"="article_id"})
     */
    public function showById(Article $article): Response
    {
        dump($article);
        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article
            ]
        );
    }

}