<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use App\Entity\User;


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
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
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
        $this->assertGreaterThan(1, $crawler->filter('script')
            ->count());
        $linkCrawler = $crawler->filter('a.dropdown-item');
        /* is there 2 navigation links */
        $this->assertGreaterThan(1, $linkCrawler->count());
        /* does one of the links contain /todo/list */
        $this->assertGreaterThan(0, $linkCrawler->filter('a[href="/todo/list"]')
            ->count());
        /* does one of the links contain /todo/list-active */
        $this->assertGreaterThan(0, $linkCrawler->filter('a[href="/todo/list-active"]')
            ->count());
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
        $link = $crawler->filter('a:contains("show")')
            -> // find all links with the text "show"
        eq(0)
            -> // select the first link in the list
        link();

        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
    }

    public function testFirstTodoContainsBackLink()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list');
        // find all links with the text "show"
        // select the first link in the list
        $link = $crawler->filter('a:contains("show")')
            -> eq(0)
            -> link();

        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('a:contains("back")')
            ->count());
    }

    public function testListActiveContainsTable()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/list-active');
        $this->assertGreaterThan(0, $crawler->filter('html table')
            ->count());
    }

    public function testListActiveContainsLink()
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
        // find all links with the text "show"
        // select the first link in the list
        $link = $crawler->filter('a:contains("show")')
            -> eq(0)
            -> link();

        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
    }
    /**
     * Post a todo : 'title' 'completed'
     * This test post a new todo and check that the number of lines in index is greater after the creation.
     */
    public function testNew()
    {
        $client = $this->client;
        $this->login();
        $crawler = $client->request('GET', '/todo/');
        $nbTodos = $crawler->filter('tr')->count();
        $crawler = $client->request('GET', '/todo/new');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Save")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Save');
        $form = $buttonCrawlernode->form(array(
            'todo' => array(
                'title' => 'Test todo',
                'completed' => False,
                'project' => 1
            )
        ));
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/todo/');
        $this->assertGreaterThan($nbTodos, $crawler->filter('tr')
            ->count());
    }
    
    /**
     * Delete last Todo
     */
    public function testDelete()
    {
        $client = $this->client;
        $this->login();
        $crawler = $client->request('GET', '/todo/');
        $nbTodos = $crawler->filter('tr')->count();
        $this->assertGreaterThan(0, $nbTodos);
        $trCrawler = $crawler->filter('tr')
        ->last()
        ->children();
        $todoId = $trCrawler->first()->text();
        $this->assertGreaterThan(0, $todoId);
        $crawler = $client->request('GET', '/todo/' . $todoId.'/edit');
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
        $client = $this->client;
        $this->login();
        $crawler = $client->request('GET', '/todo/');
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
        $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        
        $crawler = $client->request('GET', '/todo/' . $todoId);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $trCrawler = $crawler->filter('div.form-control');
        $this->assertEquals(6, count($trCrawler));
        $tdCrawler = $trCrawler->eq(2); // 3rd line completed
        $this->assertEquals('oui',$tdCrawler->text());
    }
    /* 
     * Login Function to test methods reserved to Admin
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        
        $firewallName = 'main';
        $firewallContext = $firewallName;
        $doctrine = $this->client->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        $anna = $em->getRepository(User::class)->findOneByEmail('anna@localhost');
        $token = new PostAuthenticationGuardToken($anna, $firewallName, $anna->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
