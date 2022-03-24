<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver; 
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class RegisterType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        // j ajoute chaque type à coter des champs 
            ->add('nom', TextType::class) 
            ->add('prenom', TextType::class) 
            ->add('datedeNaissance', BirthdayType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text', //pr changer le format du champ de la date 
            ]) 
            ->add('email', EmailType::class) 
            ->add('password', PasswordType::class, [
                'label' => 'mot de passe', // pr changer les noms des champs
                'mapped' => false, //pr dire qu'il n est pas obligatoire de renvoyer en BDD
                'attr' =>[
                    'placeholder' => 'Mot de passe', 
                    'class' => 'champ password'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire' 
            ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class, 
        ]);
    }
}
