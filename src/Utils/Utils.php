<?php

namespace App\Utils;

use DOMDocument;
use App\Entity\ContentImage;
use App\Services\ApiService;
use App\Entity\ArticleLangue;
use App\Entity\CategoryLangue;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Response;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Utils extends AbstractController{

    protected $em;
    protected $breadcrumbs;
    protected $apiService;


    public function __construct(EntityManagerInterface $em,Breadcrumbs $breadcrumbs,ApiService $apiService)
    {
        $this->em = $em;
        $this->breadcrumbs = $breadcrumbs;
        $this->apiService = $apiService;
    }

    public function _em(){
        return $this->em;
    }

    public function _breadcrumbs(){
        return $this->breadcrumbs;
    }

    public function thumbs_directory(){
        return $this->getParameter('thumbs_directory');
    }

    public function _persist($entity){
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function _remove($entity){
        $this->em->remove($entity);
        $this->em->flush();
    }

    public function _repository($entity){
        return $this->em->getRepository($entity);
    }

    public function _json($output){
        return new Response(json_encode($output), 200, ['Content-Type' => 'application/json']);
    }

    public function slugify($string) {
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $string);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower($clean);
        $clean = preg_replace("/[\/_|+ -]+/", "-", $clean);
        $clean = trim($clean, "-");
        setlocale(LC_ALL, $oldLocale);
        return $clean;
    }

    public function array_random($arr, $num = 1) {
        shuffle($arr);
       
        $r = array();
        for ($i = 0; $i < $num; $i++) {
            $r[] = $arr[$i];
        }
        return $num == 1 ? $r[0] : $r;
    }

    public function _getLength($entity, $name, $icon,$color, $path){
        $entities = $this->_repository($entity)->findAll();
        $length = count($entities);
        $data[] = [
            "length" => $length,
            "name" => $name,
            "icon" => $icon,
            "path" => $path,
            "color" => $color
        ];
        return $data;
    }

    public function _convertToHtml($object){

        if ($object instanceof ArticleLangue || $object instanceof CategoryLangue) {
            $target = $object instanceof ArticleLangue ? $object: $object;

            $filenames = array();
            $contentImages = $this->_repository(ContentImage::class)->findBy(['articleLangue' => $target->getId()]);

            $doc = new DOMDocument();
            try {
                $doc->loadHTML(mb_convert_encoding($target->getDescription(), 'HTML-ENTITIES', 'UTF-8'));
            } catch (\Throwable $th) {
            }
        
            $tags = $doc->getElementsByTagName('img');

            foreach ($tags as $tag) {
                // Get base64 encoded string
                $srcStr = $tag->getAttribute('src');

                if (strpos($srcStr, "CNaPS_") !== false) {
                    list($src,$fileName) = explode("uploads/summernote/",$srcStr);
                    array_push($filenames, $fileName);
                }elseif (strpos($srcStr, "http") !== false) {
                }else{
                    $base64EncData = substr($srcStr, ($pos = strpos($srcStr, 'base64,')) !== false ? $pos + 7 : 0);
                    $base64EncData = substr($base64EncData, 0, -1);
            
                    // Get an image file
                    $img = base64_decode($base64EncData);
            
                    // Get file type
                    $dataInfo = explode(";", $srcStr)[0];
                    $fileExt = str_replace('data:image/', '', $dataInfo);
            
                    // Create a new filename for the image
                    $newImageName = str_replace(".", "", uniqid("CNaPS_", true));
                    $filename = $newImageName . '.' . $fileExt;

                    $file = "uploads/summernote/". $filename;

                    // Save image to the database
                    $contentImage = new ContentImage();
                    $contentImage->setName($filename);
                    
                    if ($target instanceof ArticleLangue) {
                        $contentImage->setArticleLangue($target);
                    }else{
                        $contentImage->setCategoryLangue($target);
                    }
                    
                    $this->em->persist($contentImage);
            
                    $success = file_put_contents($file,$img);
                    
                    $base_url = new RequestContext();
                    $imgUrl = $base_url->getBaseUrl().'/uploads/summernote/' . $filename;
            
                    // Update the description thread text with an img tag for the new image
                    $newImgTag = '<img src="' . $imgUrl . '" />';
            
                    $tag->setAttribute('src', $imgUrl);
                    $tag->setAttribute('data-original-filename', $tag->getAttribute('data-filename'));
                    $tag->removeAttribute('data-filename');
                    $target->setDescription($doc->saveHTML());
                }
            }

            foreach ($contentImages as $contentImage) {
                {
                    if (in_array($contentImage->getName(), $filenames,true)) {
                    }else{
                        $this->apiService->removeFile($contentImage->getName(),"uploads/summernote/");
                        $this->em->remove($contentImage);
                    }
                }
            }
        }
    }
}
?>