<?php

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ApiService extends AbstractController
{
    protected $session;
    protected $manager;
    protected $stopwatch;
    protected $cache;
    protected $data = array();


    public function __construct(
        SessionInterface $session,
        EntityManagerInterface $manager,
        Stopwatch $stopwatch,
        CacheInterface $cache
    ) {
        $this->session = $session;
        $this->manager = $manager;
        $this->stopwatch = $stopwatch;
        $this->cache = $cache;
    }

    // CREATE UPDATE

    public function add($entityName, $entity)
    {

        if (!$entity->getId()) {

            if ($entity->getName()) {
                $name = $entity->getName();
                $this->addFlash('success', 'Ajout ' . $entityName . ' "' . $name . '" effectué avec succés');
            } else {
                $this->addFlash('success', 'Ajout ' . $entityName . ' effectué avec succés');
            }
        } else {

            $this->addFlash('success', 'Modification effectué avec succés');
        }

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    public function add_file($form, $directory, $entity, $name)
    {

        $file = $form->get($name)->getData();

        if ($file) {

            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

            $file->move(
                $this->getParameter($directory),
                $newFilename
            );
            
            $entity->setPhoto($newFilename);
        }

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    // STATUS
    public function status($entity)
    {
        $status = $entity->getStatus();
        ($status) ? $entity->setStatus(0) : $entity->setStatus(1);

        $this->manager->persist($entity);
        $this->manager->flush();
    }

    // DELETE
    public function remove_file($entity, $directory)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($directory . $entity->getPhoto());
        try {
            $this->removeFile($entity->getSrc(), "uploads/thumbs/");
        } catch (\Throwable $th) {
        }

        $this->manager->remove($entity);
        $this->manager->flush();
    }

    public function removeFile($filename, $directory)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($directory.$filename);
    }

    public function remove($entity)
    {
        $this->manager->remove($entity);
        $this->manager->flush();
    }

    public function cache($cacheName, array $data)
    {

        $this->data = $data;

        $this->stopwatch->start($cacheName);
        $this->cache->get($cacheName, function (ItemInterface $item) {

            $item->expiresAfter(5);

            // return $this->function();
            return $this->data;
        });
        $this->stopwatch->stop($cacheName);
    }

    public function make_thumb($src, $dest, $desired_width)
    {

        $exploded = explode('.', $src);
        $ext = $exploded[count($exploded) - 1];

        if (preg_match('/jpg|jpeg/i', $ext))
            $source_image = imagecreatefromjpeg($src);
        else if (preg_match('/png/i', $ext))
            $source_image = imagecreatefrompng($src);
        else if (preg_match('/gif/i', $ext))
            $source_image = imagecreatefromgif($src);
        else if (preg_match('/bmp/i', $ext))
            $source_image = imagecreatefrombmp($src);
        else
            return 0;

        /* read the source image */
        //$source_image = imagecreatefromjpeg($imageTmp);
        $width = imagesx($source_image);
        $height = imagesy($source_image);

        /* find the "desired height" of this thumbnail, relative to the desired width  */
        $desired_height = floor($height * ($desired_width / $width));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        /* create the physical thumbnail image to its destination */
        imagejpeg($virtual_image, $dest);
    }
}
