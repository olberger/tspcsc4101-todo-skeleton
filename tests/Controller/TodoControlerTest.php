<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class TodoControllerTest extends WebTestCase
{
    
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
    public function testListContainsTable()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html table')->count()
            );
    }
    public function testListTableContainsLink()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html a')->count()
            );
    }
    public function testClickOnFirstTodo()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list');
        $link = $crawler
        ->filter('a:contains("show")') // find all links with the text "Greet"
        ->eq(0) // select the second link in the list
        ->link()
        ;
        
        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
    }

    public function testFirstTodoContainsBackLink()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list');
        $link = $crawler
        ->filter('a:contains("show")') // find all links with the text "show"
        ->eq(0) // select the first link in the list
        ->link()
        ;
        
        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('a:contains("back")')->count()
            );
        
    }
    public function testListActiveContainsTable()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list-active');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html table')->count()
            );
    }
    public function testListTableActiveContainsLink()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list-active');
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('html a')->count()
            );
    }
    public function testClickOnFirstActiveTodo()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/list-active');
        $link = $crawler
        ->filter('a:contains("show")') // find all links with the text "show"
        ->eq(0) // select the first link in the list
        ->link()
        ;
        
        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
    }
    public function urlProvider()
    {
        yield ['/todo/'];
        yield ['/todo/list'];
        yield ['/todo/list-active'];
        yield ['/todo/1'];
        // ...
    }
}
