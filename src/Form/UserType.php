<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('attr' => ['class' => 'form-control']))
            ->add('forname', TextType::class, array('attr' => ['class' => 'form-control']))
            ->add('city', TextType::class, array('attr' => ['class' => 'form-control']))
            ->add('email', EmailType::class, array('attr' => ['class' => 'form-control']))
            ->add('phoneNumber', TelType::class, array('attr' => ['class' => 'form-control']))
            ->add('roles', ChoiceType::class, [
                                                'choices'  => [
                                                    'Admin' => 'ROLE_ADMIN',
                                                    'User' => 'ROLE_USER',
                                                    'Manager' => 'ROLE_MANAGER',
                                                ],
                                                'multiple' => true, 
                                                'expanded' => true, 
                                                'label' => 'Assign Roles'
                                            ])
            ->add('Valider', SubmitType::class, array('attr' => ['class' => 'btn btn-success']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
