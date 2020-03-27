<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class PasteControllerTest extends WebTestCase
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
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/paste/'];
        yield ['/paste/1'];
    }
    public function testIndexContainsTable()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/paste/');
        $this->assertGreaterThan(
            0,
            $crawler->filter('table')->count()
            );
    }
    public function testIndexContainsNew()
    {
        $client = $this->client;

        $crawler = $client->request('GET', '/paste/');
        $this->assertGreaterThan(
            0,
            $crawler->filter('a[href="/paste/new"]')->count()
            );
    }
    public function testIndexContainsEditLink()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/paste/');
        $this->assertGreaterThan(
            0,
            $crawler->filter('a:contains("Edit")')->count()
            );
    }
    public function testFirstPasteContainsLinks()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/paste/');
        $link = $crawler
        ->filter('a:contains("Show")') // find all links with the text "show"
        ->eq(0) // select the first link in the list
        ->link()
        ;
        
        // and click it
        $crawler = $client->click($link);
        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertGreaterThan(
            0,
            $crawler->filter('a:contains("Edit")')->count()
            );
        $this->assertGreaterThan(
            0,
            $crawler->filter('a:contains("Back")')->count()
            );
        $this->assertGreaterThan(
            0,
            $crawler->filter('form input[value="DELETE"]')->count()
            );
    }
    /**
     * Post a paste : 'Content', 'Created', 'content-type'
     *
     */
    public function testNew()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/paste/');
        $nbPastes = $crawler->filter('tr')->count();
        $crawler = $client->request('GET', '/paste/new');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Save")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Save');
        $form = $buttonCrawlernode->form(array(
            'paste' => array(
                'content' => 'Test paste',
                'content_type' => 'text',
                'created'=>array (
                    'date' => array( 'year' => 2020, 'month' => 4, 'day' => 14),
                    'time' => array('hour' => 14, 'minute' => 30)
                )
            )
        ));
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/paste/');
        $this->assertGreaterThan($nbPastes, $crawler->filter('tr')
            ->count());
    }
    
    /**
     * Delete last Paste
     */
    public function testDelete()
    {
        $client = $this->client;
        $crawler = $client->request('GET', '/paste/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $nbPastes= $crawler->filter('tr')->count();
        $this->assertGreaterThan(0, $nbPastes);
        $trCrawler = $crawler->filter('tr')
        ->last()
        ->children();
        $pasteId = $trCrawler->first()->text();
        $this->assertGreaterThan(0, $pasteId);
        $crawler = $client->request('GET', '/paste/' . $pasteId);
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan(0, $crawler->filter('form:contains("Delete")')
            ->count());
        $buttonCrawlernode = $crawler->selectButton('Delete');
        $form = $buttonCrawlernode->form();
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/paste/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan($crawler->filter('tr')
            ->count(), $nbPastes);
    }
}
