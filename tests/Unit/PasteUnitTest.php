<?php 
namespace App\UnitTests;

use App\Entity\Paste;
use PHPUnit\Framework\TestCase;
use DateTime;


class PasteUnitTest extends TestCase
{
    public function testGetSet()
    {
        $date = new DateTime();
        $text = 'My first Paste';
        $contentType = 'xhtml/text';
        $paste = new Paste();
        $paste->setCreated($date);
        $paste->setContent($text);
        $paste->setContentType($contentType);
        
        $this->assertEquals($date, $paste->getCreated());
        $this->assertEquals($text, $paste->getContent());
        $this->assertEquals(0, $paste->getId());
        $this->assertEquals($contentType, $paste->getContentType());
        
    }
}