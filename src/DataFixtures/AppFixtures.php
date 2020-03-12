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

use App\Entity\Todo;
use App\Entity\Paste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;



class AppFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $this->loadTodos($manager);
    }
    
    private function loadTodos(ObjectManager $manager)
    {
        foreach ($this->getTodosData() as [$title, $completed, $project]) {
            $todo = new Todo();
            $todo->setTitle($title);
            $todo->setCompleted($completed);
            $manager->persist($todo);
            
            if($project) {
                $todo->setProject($project);
            }
        }
        $manager->flush();
    }
    
    private function getTodosData()
    {
        // todo = [title, completed];
        yield ['apprendre les bases de PHP', true, $this->getReference(ProjectFixtures::CSC4101_PROJECT_REFERENCE)];
        yield ['devenir un pro du Web', false, $this->getReference(ProjectFixtures::CSC4101_PROJECT_REFERENCE)];
        yield ['monter une startup',  false, $this->getReference(ProjectFixtures::CSC4102_PROJECT_REFERENCE)];
        yield ['devenir maître du monde', false, null];
        
    }

    
    public function getDependencies()
    {
        return array(
            ProjectFixtures::class,
        );
    }
}