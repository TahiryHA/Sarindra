<?php

namespace App\Controller\Area;

use Dompdf\Dompdf;
use Dompdf\Options;
// Include Dompdf required namespaces
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/area/domp")
 */
class DompController extends Controller
{
     /**
     * @Route("/{dossierNum}", name="domp_pdf", methods={"GET","POST"})
     */
    public function index($dossierNum)
    {
        // $data = $repo->findOneBy(['DOSSIER_NUMERO' => $dossierNum]);
        if (empty($data)) {
            $this->addFlash("warning","Le document que vous cherchez n'est plus disponible!");
            return $this->redirectToRoute('sig_index');
        }      
        if ($data->getSTATUS() == false) {
                $this->addFlash("info","Ce document est en cours de traitement. Veuillez réesayer plus tard!");
            return $this->redirectToRoute('sig_index');
        }
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial'); 
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('default/mypdf.html.twig', [
            'title' => "Télé-Déclaration du ". date_format(new \DateTime(),'d/m/Y'),
            // 'data' => $data
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("createpdf.pdf", [
            "Attachment" => false // ++
        ]);

        // Store PDF Binary Data
        // $output = $dompdf->output();
        
        // // In this case, we want to write the file in the public directory
        // $publicDirectory = $this->getParameter('upload_ar');
        // // e.g /var/www/project/public/mypdf.pdf
        // $pdfFilepath =  $publicDirectory . '/' .$data->DOSSIER_NUMERO. '.pdf';
        
        // // Write file to the desired path
        // file_put_contents($pdfFilepath, $output);
        
        // Send some text response
        // return $this->redirectToRoute('index');
    }

    /**
     * @Route("/download/{docuenv}/file/{filename}", name="generate", methods={"GET","POST"})
     */
    public function generate(Request $request,$docuenv,$filename)
    {

        
        $path = $request->getSchemeAndHttpHost().'/uploads/files/'.$docuenv.'/';
        $content = file_get_contents($path.$filename);
    
        $response = new Response();
    
        //set headers
        $response->headers->set('Content-Type', 'mime/type');
        $response->headers->set('Content-Disposition', 'attachment;filename="'.$filename);
    
        $response->setContent($content);

        return $response;
    }
}