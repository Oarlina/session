<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Intern;
use App\Entity\Session;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class InternSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $a = $options['data']->getNbPlace();
        $builder
            ->add('interns', EntityType::class, [
                    'class' => Intern::class,
                    'choice_label' => 'names',
                    'multiple' => true,
                    // 'max' => $a,
                    'attr' =>['class'=> 'form-control']
                ])
            ->add('Valider', SubmitType::class, array('attr'=> ['class'=> 'btn btn-success']))
        ;
    }

    public  function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
