<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Filesystem\Filesystem;

class InitialisationOfFolder extends Fixture
{
    const BASE = "public/uploads/";
    // const BASE_CACHE = "public/media/cache/";

    public function load(ObjectManager $manager)
    {
        $filesystem = new Filesystem();
        foreach ($this->getFolders() as $folder) {
            if ($filesystem->exists($folder) !== true) {
                $filesystem->mkdir($folder, 0700);
            }
        }
        // foreach ($this->getCacheFolders() as $folder) {
        //     if ($filesystem->exists($folder) !== true) {
        //         $filesystem->mkdir($folder, 0700);
        //     }
        // }
    }

    public function getFolders() : array
    {
        return [
            [self::BASE."files/dns"],
            [self::BASE."files/pp"],

        ];
    }

    // public function getCacheFolders() : array
    // {
    //     return [
    //         [self::BASE_CACHE."article/public/images/data"],
    //         [self::BASE_CACHE."category/uploads/images"],
    //         [self::BASE_CACHE."gallery/uploads/gallery"],
    //         [self::BASE_CACHE."gallery/uploads/images"],
    //     ];
    // }
}
