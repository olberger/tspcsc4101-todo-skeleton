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
}
