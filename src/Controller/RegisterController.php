<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager){

        $this->manager = $manager; 
    }
    
        
    
    /**
     * @Route("/register", name="app_register")
     */
    public function index(Request $request): Response
    {
        // je crée 1 instance 
        $user = new User(); 
        $form = $this->createForm(RegisterType::class, $user); //création du formulaire sur la base de la class RegisterType 
        $form->handleRequest($request); //traitement du formulaire, handleRequest recup les données du formulaire (email, password) dns RegisterType.php
        if($form->isSubmitted() && $form->isValid()){ //si le formulaire et soumis et validé alors...
            $this->manager->persist($user); //persist prépare l envoi des données
            $this->manager->flush(); //on flush 
            // avec pesist et flush j envoi le resultat de mn formulaire en BDD

            dd($form->getData()); //pr vérifier ce que j ai dns mon formulaire 
        } 


        return $this->render('register/index.html.twig', [

            'myForm' => $form->createView() //j'ai send le formulaire à ma vue 
        ]); 
    }
}
