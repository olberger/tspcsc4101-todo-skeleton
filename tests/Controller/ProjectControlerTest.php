<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use App\Entity\User;



class ProjectControllerTest extends WebTestCase
{
    private $client = null;
    
    public function setUp() : void
    {
        $this->client = static::createClient();
    }
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = $this->client;
        $client->request('GET', $url);
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    public function urlProvider()
    {
        yield ['/project/'];
        yield ['/project/1'];
        // ...
    }
    /**
     * Post a project : 'Content', 'Created', 'content-type'
     *
     */
    public function testNew()
    {
        $client = $this->client;
        self::login();
        $crawler = $client->request('GET', '/project/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
                
        $nbPastes = $crawler->filter('tr')->count();
        $crawler = $client->request('GET', '/project/new');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Save")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Save');
        $form = $buttonCrawlernode->form(array(
            'project' => array(
                'title' => 'Test project',
                'description' => 'Test project'
            )
        ));
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('GET', '/project/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        
        $this->assertGreaterThan($nbPastes, $crawler->filter('tr')
            ->count());
    }
    
    /**
     * Delete last Project
     */
    public function testDelete()
    {
        $client = $this->client;
        self::login();
        $crawler = $client->request('GET', '/project/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $nbPastes= $crawler->filter('tr')->count();
        $this->assertGreaterThan(0, $nbPastes);
        $trCrawler = $crawler->filter('tr')
        ->last()
        ->children();
        $pasteId = $trCrawler->first()->text();
        $this->assertGreaterThan(0, $pasteId);
        $crawler = $client->request('GET', '/project/' . $pasteId);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Delete")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Delete');
        $form = $buttonCrawlernode->form();
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/project/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan($crawler->filter('tr')
            ->count(), $nbPastes);
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
