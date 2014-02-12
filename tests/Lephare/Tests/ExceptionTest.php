<?php

namespace Lephare\Tests;

use Silex\WebTestCase;

class ExceptionTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../src/app.php';

        $app['debug'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    public function test404()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/404');

        $this->assertEquals($client->getResponse()->getStatusCode(), 404);
    }

    public function test410()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/410');

        $this->assertEquals($client->getResponse()->getStatusCode(), 410);
    }

    public function test500()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/500');

        $this->assertEquals($client->getResponse()->getStatusCode(), 500);
    }

    public function test503()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/fr/503');

        $this->assertEquals($client->getResponse()->getStatusCode(), 503);
    }
}
