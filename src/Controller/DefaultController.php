<?php


namespace App\Controller;

use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/",name="homepage")
     */
    public function index()
    {
        return $this->render('Default/index.html.twig');
    }
}
