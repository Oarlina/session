<?php

namespace App\Form;

use App\Entity\Intern;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InternType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, array('attr'=> ['class'=> 'form-control']))
            ->add('forname', TextType::class, array('attr'=> ['class'=> 'form-control']))
            ->add('gender', TextType::class, array('attr'=> ['class'=> 'form-control']))
            ->add('city', TextType::class, array('attr'=> ['class'=> 'form-control']))
            ->add('email', TextType::class, array('attr'=> ['class'=> 'form-control']))
            ->add('phoneNumber', TextType::class, array('attr'=> ['class'=> 'form-control']))
            // ->add('sessions', EntityType::class, [
            //     'class' => Session::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
            ->add('Valider', SubmitType::class, array('attr'=>['class'=>'btn btn-success']))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Intern::class,
        ]);
    }
}
