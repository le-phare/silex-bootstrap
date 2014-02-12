<?php

namespace Lephare\Tests;

use Silex\WebTestCase;

class HomeTest extends WebTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../../../src/app.php';

        $app['debug'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    public function testHome()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/');

        $this->assertCount(1, $crawler->filter('h1:contains("Pouet")'));
    }
}
