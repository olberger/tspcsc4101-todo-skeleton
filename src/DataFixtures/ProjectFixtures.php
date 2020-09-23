<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public const CSC4101_PROJECT_REFERENCE = 'csc4101-project';
    public const CSC4102_PROJECT_REFERENCE = 'csc4102-project';
    
    public function load(ObjectManager $manager)
    {
        $this->loadProjects($manager);
    }
    
    private function loadProjects(ObjectManager $manager)
    {
        foreach ($this->getProjectsData() as [$title, $description, $reference]) {
            $project = new Project();
            $project->setTitle($title);
            $project->setDescription($description);
            $manager->persist($project);
            
            $this->addReference($reference, $project);
        }
        $manager->flush();
    }
    
    private function getProjectsData()
    {
        // project = [title, description];
        yield ['CSC4101', "Architectures et applications Web", self::CSC4101_PROJECT_REFERENCE];
        yield ['CSC4102', "Introduction au Génie Logiciel Orienté Objet", self::CSC4102_PROJECT_REFERENCE];
    }
    
    
}