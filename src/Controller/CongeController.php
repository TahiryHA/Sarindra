<?php

namespace App\Controller;

use App\Entity\Conge;
use App\Entity\Personnel;
use App\Form\CongeType;
use App\Repository\CongeRepository;
use App\Repository\DepartementRepository;
use App\Repository\PersonnelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/conge")
 */
class CongeController extends AbstractController
{
    /**
     * @Route("/", name="conge_index", methods={"GET"})
     */
    public function index(CongeRepository $congeRepository): Response
    {
        return $this->render('conge/index.html.twig', [
            'conges' => $congeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="conge_new", methods={"GET","POST"})
     */
    public function new(Request $request, PersonnelRepository $repo): Response
    {
        $conge = new Conge();
        $form = $this->createForm(CongeType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $personnel = $repo->find($request->request->get('conge')['personnel']);
            $conge->setPersonnel($personnel);
            $conge->setCreatedAt(new \DateTime());
            $entityManager->persist($conge);
            $entityManager->flush();

            return $this->redirectToRoute('conge_index');
        }

        return $this->render('conge/new.html.twig', [
            'conge' => $conge,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="conge_show", methods={"GET"})
     */
    public function show(Conge $conge): Response
    {
        return $this->render('conge/show.html.twig', [
            'conge' => $conge,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="conge_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Conge $conge): Response
    {
        $form = $this->createForm(CongeType::class, $conge);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('conge_index');
        }

        return $this->render('conge/edit.html.twig', [
            'conge' => $conge,
            'form' => $form->createView(),
            'editMode' => true
        ]);
    }

    /**
     * @Route("/api/congepersonnel", name="conge_personnel", methods={"GET","POST"},options={"expose"=true})
     */
    public function onChange(Request $request, DepartementRepository $repo): Response
    {
        $data = [];
        $id = $request->request->get('conge')['departement'];
        $departement = $repo->findOneBy(['id' => $id]);
        $personnels = $departement->getPersonnels();
        $select = '<select id="conge_personnel" name="conge[personnel]" class="form-control">';
        $select .= '<option value="" disabled>Selectionnez votre personnel</option>';
        foreach ($personnels as $key => $value) {
            $select .= '<option value="'.$value->getId().'">'.$value->getName().'</option>';
        }
        $select .= '</select>';
        // dd($select);

        return new Response($select, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/departementcategorie", name="departement_categorie", methods={"GET","POST"},options={"expose"=true})
     */
    public function onChangeDepartementCategorie(Request $request, DepartementRepository $repo): Response
    {
        $data = [];
        $id = $request->request->get('personnel')['departement'];
        $departement = $repo->findOneBy(['id' => $id]);
        $personnels = $departement->getCategories();
        $select = '<select id="personnel_categorie" name="personnel[categorie]" class="form-control">';
        $select .= '<option value="" disabled>Selectionnez votre catégorie</option>';
        foreach ($personnels as $key => $value) {
            $select .= '<option value="'.$value->getId().'">'.$value->getName().'</option>';
        }
        $select .= '</select>';
        // dd($select);

        return new Response($select, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="conge_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Conge $conge): Response
    {
        if ($this->isCsrfTokenValid('delete' . $conge->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($conge);
            $entityManager->flush();
        }

        return $this->redirectToRoute('conge_index');
    }
}
