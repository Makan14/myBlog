<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager){

        $this->manager = $manager; 
    }
    /**
     * @Route("/", name="app_home") 
     */
    public function index(Request $request): Response  
    {
        //logique stocker dns 1 variable avc tt ls articles
        $articles = $this->manager->getRepository(Article::class)->findAll();  

        // dd($articles);

        return $this->render('home/index.html.twig', [
            'articles' => $articles, 
        ]);  
    }
}
