<?php

namespace App\Controller\Area;

use App\Utils\Utils;
use App\Entity\Parameter;
use App\Form\ParameterType;
use App\Services\ApiService;
use App\Repository\ParameterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/parameter")
 */
class ParameterController extends AbstractController
{

    protected $apiService;
    protected $em;
    protected $breadcrumbs;
    
    public function __construct(ApiService $apiService,Utils $utils,EntityManagerInterface $em)
    {
        $this->apiService = $apiService;
        $this->breadcrumbs = $utils->_breadcrumbs();
        $this->em = $em;
    }
    
    /**
     * @Route("/", name="parameter_index", methods={"GET"})
     */
    public function index(ParameterRepository $parameterRepository): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        $this->breadcrumbs->prependRouteItem("Acceuil", "index");
        $this->breadcrumbs->addItem("Paramètre");
        $this->breadcrumbs->addItem("Liste");

        return $this->render('area/parameter/index.html.twig', [
            'parameters' => $parameterRepository->findAll(),
            'page_parent'=> "Paramètre",
            'page_child' => "Liste",
            'page_action' => null,
        ]);
    }

    /**
     * @Route("/new", name="parameter_new", methods={"GET","POST"})
     * @Route("/{id}/edit", name="parameter_edit", methods={"GET","POST"})
     */
    public function form(Parameter $parameter = null, Request $request): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        if (!$parameter) {
            
            $page_parent = "Paramètre" ;
            $page_child = "Liste";
            $page_action = "Ajout";
            $editMode = false;
            $parameter = new Parameter();
            $parameter->setId($this->em);

        }
        else{

            $page_parent = "Paramètre";
            $page_child = "Liste";
            $page_action = "Modification";
            $editMode = true;
            

        }

        $this->breadcrumbs->prependRouteItem("Acceuil", "index");
        $this->breadcrumbs->addRouteItem("Paramètre","parameter_index");
        $this->breadcrumbs->addItem($page_action);

        $form = $this->createForm(ParameterType::class, $parameter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $this->apiService->add('paramètre',$parameter);

            return $this->redirectToRoute('parameter_index');
        }

        return $this->render('area/parameter/new.html.twig', [
            'parameter' => $parameter,
            'form' => $form->createView(),
            'page_parent'=> $page_parent,
            'page_child' => $page_child,
            'page_action' => $page_action,
            'editMode' => $editMode
        ]);
    }

    /**
     * @Route("/{id}", name="parameter_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Parameter $parameter): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('index');
        }

        if ($this->isCsrfTokenValid('delete'.$parameter->getId(), $request->request->get('_token'))) {

            $this->apiService->remove($parameter);        

        }

        return $this->redirectToRoute('parameter_index');
    }
}
