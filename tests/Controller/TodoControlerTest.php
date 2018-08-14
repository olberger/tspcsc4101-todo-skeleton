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

    public function urlProvider()
    {
        yield ['/todo/'];
        yield ['/todo/list'];
        yield ['/todo/list-active'];
        yield ['/todo/1'];
        // ...
    }
    /**
     * Post a todo : 'title'
     * 'completed'
     */
    public function testNew()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        
        $nbTodos = $crawler->filter('tr')->count();
        $crawler = $client->request('GET', '/todo/new');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Save")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Save');
        $form = $buttonCrawlernode->form(array(
            'todo' => array(
                'completed' => false,
                'title' => 'Test Todo'
            )
        ));
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/todo/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan($nbTodos, $crawler->filter('tr')
            ->count());
    }
    
    /**
     * Delete last Todo
     */
    public function testDelete()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $nbTodos = $crawler->filter('tr')->count();
        $this->assertGreaterThan(0, $nbTodos);
        $trCrawler = $crawler->filter('tr')
        ->last()
        ->children();
        $todoId = $trCrawler->first()->text();
        $this->assertGreaterThan(0, $todoId);
        $crawler = $client->request('GET', '/todo/' . $todoId);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Delete")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Delete');
        $form = $buttonCrawlernode->form();
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/todo/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan($crawler->filter('tr')
            ->count(), $nbTodos);
    }
    
    /**
     * Update last Todo set completed true
     */
    public function testUpdate()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/todo/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $nbTodos = $crawler->filter('tr')->count();
        $this->assertGreaterThan(0, $nbTodos);
        $trCrawler = $crawler->filter('tr')
        ->last()
        ->children();
        $todoId = $trCrawler->first()->text();
        $this->assertGreaterThan(0, $todoId);
        $crawler = $client->request('GET', '/todo/' . $todoId . '/edit');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Update")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Update');
        $form = $buttonCrawlernode->form(array(
            'todo' => array(
                'completed' => true,
            )
        )
            );
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/todo/' . $todoId);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $trCrawler = $crawler->filter('tr')->first(); // 1st line Id
        $trCrawler = $trCrawler->nextAll(); // 2nd line title
        $trCrawler = $trCrawler->nextAll(); // 3rd line completed
        $tdCrawler = $trCrawler->children(); // contains th and td
        $this->assertEquals($tdCrawler->last()
            ->text(), 'Yes');
    }
}