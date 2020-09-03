<?php

namespace App\Form;

use App\Entity\Conge;
use App\Entity\Personnel;
use App\Entity\Departement;
use Symfony\Component\Form\AbstractType;
use App\Repository\DepartementRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CongeType extends AbstractType
{
    private $repo;

    public function __construct(DepartementRepository $repo)
    {
        $this->repo = $repo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
           
        
        ->add('departement',EntityType::class,[
            'class' => Departement::class,
            'placeholder' => 'Selectionnez votre département',

            'choice_label' => 'name',
            'mapped' => false,
            'required' => true
            
        ])
        ->add('personnel',EntityType::class,[
            'class' => Personnel::class,
            'placeholder' => 'Selectionnez votre personnel',
            'choice_label' => 'name',
            // 'mapped' => false,
            'required' => true
            
        ])
        ->add('motif',TextType::class,[
            'attr' => [
                'placeholder' => 'Motif'
            ]
        ])
        ->add('dateDep', DateType::class, [
            'widget' => 'single_text',
            // this is actually the default format for single_text
            'format' => 'yyyy-MM-dd',
            'data' => new \DateTime('now'),

        ])
        ->add('dateFin', DateType::class, [
            'widget' => 'single_text',
            // this is actually the default format for single_text
            'format' => 'yyyy-MM-dd',
            // 'data' => new \DateTime('now'),

        ])
        ->add('status',ChoiceType::class,[
            'choices' => [
                'Non approuvé' => 0,
                'Approuvé' => 1,
            ]
        ])
        ->add('observation',TextareaType::class,[
            'required' => false
        ])



           
            // ->add('createdAt',DateType::class,[
            //     'data' => new \DateTime()
            // ])
            
        ;

    }

    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Conge::class,
        ]);
    }
}
