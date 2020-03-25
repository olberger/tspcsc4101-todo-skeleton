<?php 
namespace App\UnitTests;

use App\Entity\Project;
use App\Entity\Todo;
use PHPUnit\Framework\TestCase;
use DateTime;


class ProjectUnitTest extends TestCase
{
    private $project;
    private const TEXT = 'My first Project';
    public function setup() : void
    {
        $this->project = new Project();
        
        $this->project = new Project();
        $this->project->setTitle(self::TEXT);
        $this->project->setDescription(self::TEXT);
    }
    public function testGetSet()
    {
           
        // assert that your calculator added the numbers correctly!
        $this->assertEquals(self::TEXT, $this->project->getDescription());
        $this->assertEquals(self::TEXT, $this->project->getTitle());
        $this->assertEquals(0, $this->project->getId());
        $this->assertEquals(0, $this->project->getTodos()->count());
     }
     public function testArrayTodo(){
         $date = new DateTime();
         $text = 'My first Todo';
         $todo = new Todo();
         $todo->setCreated($date);
         $todo->setUpdated($date);
         $todo->setTitle($text);
         $this->project->addTodo($todo);
         $this->assertEquals($this->project, $todo->getProject());
         
         $this->project->addTodo(clone $todo);
         $this->project->addTodo(clone $todo);
         $this->assertEquals(3, $this->project->getTodos()->count());
         $this->project->removeTodo($todo);
         $this->assertEquals(2, $this->project->getTodos()->count());       
     }
}