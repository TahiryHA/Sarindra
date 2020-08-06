<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Personnel;
use App\Entity\Departement;
use App\Repository\CategorieRepository;
use App\Repository\DepartementRepository;
use App\Repository\PersonnelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonnelType extends AbstractType
{

    private $repo;

    public function __construct(DepartementRepository $repo)
    {
        $this->repo = $repo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        dump($options['data']->getCategorie());
        $builder
            ->add('name',TextType::class,[
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom'
                ]
            ])
            ->add('departement',EntityType::class,[
                'class' => Departement::class,
                'placeholder' => 'Selectionnez votre département',
                // 'mapped' => false,
                'required' => true,
                'choice_label' => 'name',
                // 'multiple' => true
                
            ])
            ->add('categorie',EntityType::class,[
                'class' => Categorie::class,
                'placeholder' => 'Selectionnez votre catégorie',
                // 'mapped' => false,
                'required' => true,
                'choice_label' => 'name',
                
            ])
            
            
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Personnel::class,
        ]);
    }
}
