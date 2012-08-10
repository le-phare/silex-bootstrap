<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.options' => array('cache' => __DIR__.'/../cache'),
));

$app->register(new TranslationServiceProvider(), array(
    'locale_fallback' => 'fr',
));

$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    if (file_exists(__DIR__.'/locales/messages.en.yml')) {
      $translator->addResource('yaml', __DIR__.'/locales/messages.en.yml', 'en');
    }

    if (file_exists(__DIR__.'/locales/messages.de.yml')) {
      $translator->addResource('yaml', __DIR__.'/locales/messages.de.yml', 'de');
    }

    if (file_exists(__DIR__.'/locales/messages.fr.yml')) {
      $translator->addResource('yaml', __DIR__.'/locales/messages.fr.yml', 'fr');
    }

    return $translator;
}));

return $app;
