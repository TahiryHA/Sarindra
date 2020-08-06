<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class CategorieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class,[
                'attr' => [
                    'placeholder' => 'Name'
                ]
            ])
            ->add('salaire',IntegerType::class,[
                'attr' => [
                    'placeholder' => 'Salaire'
                ]
            ])
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
            'data_class' => Categorie::class,
        ]);
    }
}
