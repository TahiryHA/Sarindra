<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('present')
            ->add('absent')
            ->add('createdAt')
            ->add('departement',EntityType::class,[
                'label' => false,
                'class' => Departement::class,
                'choice_label' => 'name',
                // 'multiple' => true
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
