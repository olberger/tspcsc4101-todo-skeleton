<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class TodoControllerTest extends WebTestCase
{
    private $client = null;
    
    public function setUp() : void
    {
        $this->client = static::createClient();
    }
    /**
     * @dataProvider urlProvider
     */
    public function testPublicPageIsSuccessful($url)
    {
        $client = $this->client;
        $client->request('GET', $url);
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/todo/'];
        yield ['/todo/list'];
        yield ['/todo/list-active'];
        yield ['/todo/1'];
    }
    public function testIndexPage()
    {
       $client = $this->client;
       $crawler = $client->request('GET', '/');
        /* is there 2 link to load css pages */
        $this->assertGreaterThan(1, $crawler->filter('link')
            ->count());
        /* is there 2 script to load js */
        $this->assertGreaterThan(
            1,
            $crawler->filter('script')->count()
            );
        $linkCrawler = $crawler->filter('a.nav-link');
        /* is there 2 navigation links */
        $this->assertGreaterThan(
            1,
            $linkCrawler->count()
            );
        /* does one of the links contain /todo/list */
        $this->assertGreaterThan(
            0,
            $linkCrawler->filter('a[href="/todo/list"]')->count()
            );
        /* does one of the links contain /todo/list-active */
        $this->assertGreaterThan(
            0,
            $linkCrawler->filter('a[href="/todo/list-active"]')->count()
            );
        
    }

    public function testListContainsTable()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list');
        $this->assertGreaterThan(0, $crawler->filter('table')
            ->count());
    }

    public function testListTableContainsLink()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list');
        $this->assertGreaterThan(0, $crawler->filter('html a')
            ->count());
    }

    public function testClickOnFirstTodo()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list');
        $link = $crawler->filter('a:contains("Show")')
            -> // find all links with the text "Show"
        eq(0)
            -> // select the first link in the list
        link();

        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()->isSuccessful());
        
    }

    public function testFirstTodoContainsBackLink()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list');
        // find all links with the text "Show"
        // select the first link in the list
        $link = $crawler->filter('a:contains("Show")')
            -> eq(0)
            -> link();

        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Back")')
            ->count());
    }

    public function testListActiveContainsTable()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list-active');
        $this->assertGreaterThan(0, $crawler->filter('html table')
            ->count());
    }
    public function testListTableActiveContainsLink()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list-active');
        $this->assertGreaterThan(0, $crawler->filter('html a')
            ->count());
    }

    public function testClickOnFirstActiveTodo()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list-active');
        // find all links with the text "Show"
        // select the first link in the list
        $link = $crawler->filter('a:contains("Show")')
            -> eq(0)
            -> link();

        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
    }

}
