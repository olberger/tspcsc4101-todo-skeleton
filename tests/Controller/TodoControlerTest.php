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
    
    public function setUp()
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
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
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
     * This test post a new todo and check that the number of lines in index is greater after the creation.
     */
    public function testNew()
    {
        $this->logIn();
        $client = $this->client;
        $crawler = $client->request('GET', '/todo/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $nbTodos = $crawler->filter('tr')->count();
        $crawler = $client->request('GET', '/login');
        $buttonCrawlernode = $crawler->selectButton('Save');
        $form = $buttonCrawlernode->form(array(
            '' => array(
                'email' => 'anna@localhost',
                'passwd' => 'anna',
                '_token' => $this->csrf_token('authenticate')
            )
        ));
        $crawler = $client->request('GET', '/todo/new');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
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
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/todo/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertGreaterThan($nbTodos, $crawler->filter('tr')
            ->count());
    }
    
    /**
     * Delete last Todo
     */
    public function testDelete()
    {
        $client = $this->client;
        $this->logIn();
        $crawler = $client->request('GET', '/todo/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
        $nbTodos = $crawler->filter('tr')->count();
        $this->assertGreaterThan(0, $nbTodos);
        $trCrawler = $crawler->filter('tr')
        ->last()
        ->children();
        $todoId = $trCrawler->first()->text();
        $this->assertGreaterThan(0, $todoId);
        $crawler = $client->request('GET', '/todo/' . $todoId);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()
            ->getStatusCode());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Delete")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Delete');
        $form = $buttonCrawlernode->form();
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/todo/');
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()
            ->getStatusCode());
        $this->assertGreaterThan($crawler->filter('tr')
            ->count(), $nbTodos);
    }
    
    /**
     * Update last Todo set completed true
     */
    public function testUpdate()
    {
        $client = $this->client;
        $this->logIn();
        
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
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()
            ->getStatusCode());
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
    /* 
     * Login Function to test methods reserved to Admin
     */
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');
        
        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'guard';
        
        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $admin = new User();
        $admin->setEmail('anna@localhost');
        $admin->setPassword('anna');
        $admin->addRole('ROLE_ADMIN');
        $token = new PostAuthenticationGuardToken($admin, 'guard', $admin->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();
        
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
