<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Intern;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nameSession', TextType::class, array('attr' =>['class'=> 'form-control']))
            ->add('beginSession', null, [
                'widget' => 'single_text',
                'attr' =>['class'=> 'form-control']
            ])
            ->add('finishSession', null, [
                'widget' => 'single_text',
                'attr' =>['class'=> 'form-control']
            ])
            ->add('nbPlace',IntegerType::class,  array( 
                'attr' => [
                    'min' => 1, // ma valeur minimum est de 1 afin d'éviter de créer une session ou personne ne peut être inscrit
                    'class'=> 'form-control']
            ))
            // ->add('user', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            //     'attr' =>['class'=> 'form-control']
            // ])
            // ->add('interns', EntityType::class, [
            //     'class' => Intern::class,
            //     'choice_label' => 'names',
            //     'multiple' => true,
            //     'attr' =>['class'=> 'form-control']
            // ])
            ->add('Valider', SubmitType::class, array('attr'=> ['class' => 'btn btn-success']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
