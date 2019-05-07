<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog/show/{slug}",requirements={"slug"="[a-z0-9-]+"})
     * @return Response
     */
    public function show($slug = 'Article Sans Titre'): Response
    {
        return $this->render('blog/show.html.twig',
            ['show' =>ucwords(str_replace('-',' ',$slug))]
        );
    }
}