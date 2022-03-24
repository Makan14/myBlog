<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    public function __construct(EntityManagerInterface $manager){

        $this->manager = $manager; 
    } 

    /**
     * @Route("/user", name="app_user")
     */
    public function index(Request $request): Response 
    {

        $users = $this->manager->getRepository(User::class)->findAll();  

        return $this->render('user/index.html.twig', [
            'users' => $users, 
        ]);


    }

    /**
     * @Route("/admin/user/delete/{id}", name="app_user_delete")
     */
    public function userDelete(User $user): Response 
    {

        // $users = $this->manager->getRepository(User::class)->findAll();   
        $this->manager->remove($user); 
        $this->manager->flush();  
        return $this->redirectToRoute('app_user'); 


    }

    /**
     * @Route("/user/edit/{id}", name="app_user_edit")
     */
    public function userEdit(User $user, Request $request): Response
    {
        $form = $this->createForm(RegisterType::class, $user); 
        $form->handleRequest($request); 
        if ($form->isSubmitted() && $form->isValid()) {
            $emptyPassword = $form->get('password')->getData();//recup le champ password;
            // qund le formulaire et soumis verifier le champ password
            // si il et vide alors ont recup le mdp de l utilisateur à modif et on le renvoi

            if ($emptyPassword == null) {
                // recup le mdp utilisateur en BDD et le renvoyer 
                $user->setPassword($user->getPassword());  
                // setPassword envoi en BDD / getPassword recup en BDD 

            }

            $this->manager->persist($user); 
            $this->manager->flush(); 
            return $this->redirectToRoute('app_user'); 

            // dd($form->getData()); 
        };

        return $this->render('user/editUser.html.twig', [
            'form' => $form->createView() 
        ]);

    }
}
