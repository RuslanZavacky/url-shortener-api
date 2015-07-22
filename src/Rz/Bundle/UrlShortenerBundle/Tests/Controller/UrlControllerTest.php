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
            'url' => 'http://opticsplanet.com',
            'data' => [
                'user_id' => 123
            ],
            'query_params' => [
                'utm_source' => 'source',
                'utm_label' => 'label',
                'utm_action' => 'action'
            ]
        ];

        $crawler = $client->request('POST', '/api/1.0/urls', [], [], [], json_encode($params));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
