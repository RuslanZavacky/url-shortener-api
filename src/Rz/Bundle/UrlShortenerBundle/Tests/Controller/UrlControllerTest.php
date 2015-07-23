<?php

namespace Rz\Bundle\UrlShortenerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UrlControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/go/random');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testPost()
    {
        $client = static::createClient();

        $params = [
            'url' => 'http://opticsplanet.com'
        ];

        $crawler = $client->request('POST', '/api/v1/urls', [], [], [], json_encode($params));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }
}
