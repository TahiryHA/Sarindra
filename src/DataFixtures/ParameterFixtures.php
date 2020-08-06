<?php

namespace App\DataFixtures;

use App\Entity\Parameter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParameterFixtures extends Fixture
{
    
    public function load(ObjectManager $manager)
    {
        foreach ($this->getParameters() as [$value,$classCode, $subjectCodes]) {
            $params = new Parameter();
            $params->setName($classCode);
            $params->setCode('PARAMETER.' . $subjectCodes);
            $params->setValue($value);

            $manager->persist($params);
        }

        $params = new Parameter();
        $params->setName('Email');
        $params->setCode('PARAMETER.EMAIL');
        $params->setValue('admin@cnaps.com');

        $manager->persist($params);

        $manager->flush();
    }

    public function getParameters(): array
    {
        return [
            [128000,'Adhérents', 'ADHERENTS'],
            [258,'Entreprise', 'ENTREPRISE'],
            [18,'Partenaires', 'PARTENAIRES'],
        ];
    }
}
