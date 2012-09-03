<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use SilexExtension\AsseticExtension;

$app = new Silex\Application();

$app['debug'] = true;

//
// Twig
//
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.options' => array('cache' => false),
));

//
// Translator
//
$app->register(new TranslationServiceProvider(), array(
    'locale'          => 'fr',
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

//
// Assetic
//
$app->register(new AsseticExtension(), array(
    'assetic.class_path' => __DIR__.'/vendor/assetic/src',
    'assetic.path_to_web' => __DIR__ . '/../web',
    'assetic.options' => array(
        'debug' => $app['debug'],
        'auto_dump_assets' => true,
    ),
    'assetic.filters' => $app->protect(function($fm) {
        $fm->set('less', new Assetic\Filter\LessphpFilter());
    })
));

return $app;
