<?php

require_once __DIR__.'/../vendor/autoload.php';

use Silex\Provider\TwigServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use SilexAssetic\AsseticExtension;

$app = new Silex\Application();

$app['debug'] = true;

//
// Twig
//
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.options' => array( 'cache' => __DIR__.'/../cache/twig'),
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
        'debug' => false
    ),
    'assetic.filters' => $app->protect(function($fm) {
        $fm->set('lessphp', new Assetic\Filter\LessphpFilter());
    }),
    'assetic.assets' => $app->protect(function($am, $fm) {
        $am->set('styles', new Assetic\Asset\AssetCache(
            new Assetic\Asset\AssetCollection(
                new Assetic\Asset\FileAsset(__DIR__ . '/../web/less/styles.less', array($fm->get('lessphp'))),
                new Assetic\Asset\FileAsset(__DIR__ . '/../web/less/project.less', array($fm->get('lessphp')))
            ),
            new Assetic\Cache\FilesystemCache(__DIR__.'/../cache/assetic')
        ));
        $am->get('styles')->setTargetPath('compiled/styles.css');
    })
));

return $app;
