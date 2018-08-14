<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class PasteControllerTest extends WebTestCase
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
        yield ['/paste/'];
        yield ['/paste/1'];
        // ...
    }
    /**
     * Post a paste : 'Content', 'Created', 'content-type'
     *
     */
    public function testNew()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/paste/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        
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
                    'date' => array( 'year' => 2018, 'month' => 8, 'day' => 14),
                    'time' => array('hour' => 14, 'minute' => 30)
                )
            )
        ));
        $crawler = $client->submit($form);
        $this->assertTrue($client->getResponse()
            ->isRedirect());
        $crawler = $client->request('GET', '/paste/');
        $this->assertTrue($client->getResponse()
            ->isSuccessful());
        $this->assertGreaterThan($nbPastes, $crawler->filter('tr')
            ->count());
    }
    
    /**
     * Delete last Todo
     */
    public function testDelete()
    {
        $client = self::createClient();
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