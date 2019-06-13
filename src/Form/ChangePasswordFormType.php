<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Confirmer votre Pseudo',
                ],
                'constraints' => [new NotBlank(['message'=>'Veuillez compléter ce champs'])]
            ])

            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe ne sont pas identiques',
                'options' => ['attr' => ['class' => 'mot de passe']],
                'required' =>true,
                'first_options' => ['label' => false, 'attr' => [
                    'placeholder' => 'Nouveau mot de passe',
                ],],
                'second_options' => ['label' => false,'attr' => [
                    'placeholder' => 'Répéter votre nouveau mot de passe',
                ],]
            ]);
    }
}
