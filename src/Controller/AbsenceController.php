<?php

namespace App\Controller;

use App\Entity\Absence;
use App\Entity\Departement;
use App\Entity\Personnel;
use App\Form\AbsenceType;
use App\Form\AbsenceCriteriaType;
use App\Repository\AbsenceRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/absence")
 */
class AbsenceController extends AbstractController
{

    /**
     * @Route("/", name="absence_index", methods={"GET"})
     */
    public function index(AbsenceRepository $absenceRepository): Response
    {
        return $this->render('absence/index.html.twig', [
            'absences' => $absenceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/departement", name="absence_criteria", methods={"GET","POST"})
     */
    public function criteria(Request $request, AbsenceRepository $absenceRepository): Response
    {
        $absence = array("message" => "");
        $form = $this->createForm(AbsenceCriteriaType::class, $absence);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            
            $data = $form->getData();



            return $this->redirectToRoute("absence_new",['id'=> $data['departement']->getId()]);
        }


        $personnels = $this->getDoctrine()->getManager()->getRepository(Personnel::class)->findAll();
        foreach ($personnels as $key => $value) {
            $data = [
                $value->getId() => $value->getName()
            ];
        }

        return $this->render('absence/criteria.html.twig', [
            'absences' => $absenceRepository->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/new", name="absence_new", methods={"GET","POST"})
     */
    public function new(Request $request, Departement $departement): Response
    {
        if ($request->getMethod() == "POST"){

            $absence = new Absence();

            $status = $request->request->get('status');


            // dd($request->request);


            $presents = [];
            $absents = [];
            $data = [];


                foreach ($status as $i => $val) {
                   
                        if ($val) {
                            $presents[] = $i;
                            // $presents[] = $this->getDoctrine()->getManager()->getRepository(Student::class)->findOneBy(['id' => $i])->getName(); 
                        }else{
                            $absents[] = $i;
                            // $absents[] = $this->getDoctrine()->getManager()->getRepository(Student::class)->findOneBy(['id' => $i])->getName(); 

                        }
                        
                }
    
            $absence->setDepartement($departement);
            $absence->setPresent($presents);
            $absence->setAbsent($absents);
            $absence->setCreatedAt(new \DateTime());


            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($absence);
            $entityManager->flush();
    
            return $this->redirectToRoute('absence_index');
        }

        return $this->render('absence/new.html.twig', [
            'departement' => $departement,
        ]);
    }

    /**
     * @Route("/{id}", name="absence_show", methods={"GET"})
     */
    public function show(Absence $absence): Response
    {
        return $this->render('absence/show.html.twig', [
            'absence' => $absence,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="absence_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Absence $absence): Response
    {
        $form = $this->createForm(AbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('absence_index');
        }

        return $this->render('absence/edit.html.twig', [
            'absence' => $absence,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="absence_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Absence $absence): Response
    {
        if ($this->isCsrfTokenValid('delete'.$absence->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($absence);
            $entityManager->flush();
        }

        return $this->redirectToRoute('absence_index');
    }
}
