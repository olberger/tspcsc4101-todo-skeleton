<?php 
namespace App\UnitTests;

use App\Entity\Todo;
use PHPUnit\Framework\TestCase;
use DateTime;


class TodoUnitTest extends TestCase
{
    public function testGetSet()
    {
        $date = new DateTime();
        $text = 'My first Todo';
        $todo = new Todo();
        $todo->setCreated($date);
        $todo->setUpdated($date);
        $todo->setTitle($text);
        
        // assert that your calculator added the numbers correctly!
        $this->assertEquals($date, $todo->getCreated());
        $this->assertEquals($date, $todo->getUpdated());
        $this->assertEquals($text, $todo->getTitle());
        $this->assertEquals(0, $todo->getId());
        $this->assertEquals(0, $todo->getCompleted());
        $todo->setCompleted(true);
        $this->assertEquals(true, $todo->getCompleted());
        
    }
}