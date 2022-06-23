<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Entity\Commentaire;
use App\Form\CommentaireType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArticleController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager){

        $this->manager = $manager; 
    }
    
    /**
     * @Route("/admin/all/article", name="app_all_article") 
     */
    public function allArticle(): Response 
    {
        //logique stocker dns 1 variable avc tt ls articles
        $articles = $this->manager->getRepository(Article::class)->findAll();  

        // dd($articles);

        return $this->render('article/allArticle.html.twig', [
            'articles' => $articles, 
        ]);  
    
    }

    /**
     * @Route("/article", name="app_article")
     */
    public function index(Request $request, SluggerInterface $slugger): Response
    {
        $article = new Article(); //nvl instance de article 
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $photoArticle = $form->get('photo')->getData();
       
            if($photoArticle){
           $originalFilename = pathinfo($photoArticle->getClientOriginalName(),PATHINFO_FILENAME);
           $safeFilename = $slugger->slug($originalFilename);
           $newFilename = $safeFilename.'-'.uniqid().'.'.$photoArticle->guessExtension();
             try {
                $photoArticle->move(
                    $this->getParameter('photo'),
                    $newFilename
                );
             }catch (FileException $e){

             }
              $article->setPhoto($newFilename);
            }else{
                dd('aucune photo disponible');
            }

            $article->setAuteur($this->getUser()->getNomComplet()); 
            $article->setPublication(new \datetime); //pr send automatiquement la date d'aujourd hui
            
            $this->manager->persist($article); 
            $this->manager->flush($article);

            // dd($form->getData()); 
        }
        

        return $this->render('article/index.html.twig', [

            'formArticle' => $form->createView()
        ]);
    }

    /**
     * @Route("/article/delete/{id}", name="app_article_delete")
     */
    public function articleDelete(Article $article): Response
    {
        $this->manager->remove($article); 
        $this->manager->flush();  
        return $this->redirectToRoute('app_home'); 

    }

    /**
     * @Route("/article/edit/{id}", name="app_article_edit")
     */
    public function articleEdit(Article $article, Request $request): Response 
    {
        $form = $this->createForm(ArticleType::class, $article); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            $photoArticle = $form->get('photo')->getData();
       
            if($photoArticle){
           $originalFilename = pathinfo($photoArticle->getClientOriginalName(),PATHINFO_FILENAME);
           $safeFilename = $slugger->slug($originalFilename);
           $newFilename = $safeFilename.'-'.uniqid().'.'.$photoArticle->guessExtension();
             try {
                $photoArticle->move(
                    $this->getParameter('photo'), 
                    $newFilename
                );
             }catch (FileException $e){

             }
              $article->setPhoto($newFilename);
            }else{
                dd('aucune photo disponible');
            }

            $article->setPublication(new \datetime); //pr send automatiquement la date d'aujourd hui
            $this->manager->persist($article); 
            $this->manager->flush(); 
            return $this->redirectToRoute('app_home'); 

            // dd($form->getData()); 
        };

        return $this->render('article/editArticle.html.twig', [

            'formArticle' => $form->createView() 
        ]); 

    }

      /**
     * @Route("/single/article/{id}", name="app_view_article") 
     */
    public function singleArticle(Article $article, Request $request): Response 
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) { 

            // j envoi la date l auteur et l utilisateur en BDD 
            $commentaire->setDate(new \DateTime()); 
            $commentaire->setAuteur($this->getUser()); 
            $commentaire->setArticle($article);
            $this->manager->persist($commentaire); 
            $this->manager->flush(); 
            return $this->redirectToRoute('app_view_article', [
                'id' => $article->getId(),
            ]); 

            // dd($form->getData()); 
        };

        return $this->render('article/singleArticle.html.twig', [
            'article' => $article,
            'form'=>$form->createView() 
        ]);  
    
    }
}
