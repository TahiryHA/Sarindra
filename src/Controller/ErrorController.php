<?php

namespace App\Controller;

use App\Utils\Utils;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ErrorController extends AbstractController
{
    protected $utils;

    public function __construct(Utils $utils)
    {
        $this->utils = $utils;
    }

    public function show(FlattenException $exception,Request $request){
        $statusCode = $exception->getStatusCode();
        $previous = $exception->getPrevious();
        $statusMessage =  $exception->getMessage();  
    
        if ($statusCode == 500) {

                if ($previous !== null){

                    if ($previous->getCode() == 1049) {

                        $statusMessage = $previous->getMessage();  

                    }

                }
                elseif( strpos($request->getPathInfo(),'area')){
                    $statusMessage = "Erreur interne du serveur.";
                }else{
                    // $url = $request->getSchemeAndHttpHost().$this->generateUrl('index');
                    // return new RedirectResponse($url);
                }
            
        }
        if($statusCode == 404){

            $statusMessage = "Ressource non trouvée.";

        }

        return $this->render("error/index.html.twig", [
            "status_code" => $statusCode,
            "status_message" => $statusMessage,
        ]);
    }
}