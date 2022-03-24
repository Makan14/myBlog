<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHash){

        $this->manager = $manager; 
        $this->passwordHash = $passwordHash; 
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
         
            $emptyPassword = $form->get('password')->getData(); 
            
            if ($emptyPassword == null) {
                // recup le mdp utilisateur en BDD et le renvoyer 
                $user->setPassword($user->getPassword());  
                // setPassword envoi en BDD / getPassword recup en BDD                
            }else {
                $passewordEncod = $this->passwordHash->hashPassword($user , $emptyPassword);
                $user->setPassword($passewordEncod); 
            }
            
            $this->manager->persist($user); //persist prépare l envoi des données 
            $this->manager->flush(); //on flush 
            // avec persist et flush j envoi le resultat de mn formulaire en BDD

            // pr retourner sur la page login apres s etre inscrit
            return $this->redirectToRoute('app_login'); 
             
        } 

        return $this->render('register/index.html.twig', [

            'myForm' => $form->createView() //j'ai send le formulaire à ma vue 
        ]); 
    }
}
