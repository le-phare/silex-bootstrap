<?php

require_once __DIR__.'/../vendor/autoload.php';

use Assetic\Extension\Twig\AsseticExtension;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Silex\Application();

$app['debug'] = true;

//
// Twig
//
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/templates',
    'twig.options' => array(
        'cache' => __DIR__.'/../cache/twig',
        'debug' => $app['debug']
    ),
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
$app->register(new SilexAssetic\AsseticServiceProvider());

$app['assetic.path_to_web'] = __DIR__ . '/../web';
$app['assetic.options'] = array(
    'debug' => true,
    'auto_dump_assets' => true,
);
$app['assetic.class_path'] = __DIR__.'/vendor/assetic/src';

$app['assetic.filter_manager'] = $app->share(
    $app->extend('assetic.filter_manager', function($fm, $app) {
        $fm->set('lessphp', new Assetic\Filter\LessphpFilter());

        return $fm;
    })
);

$app['assetic.asset_manager'] = $app->share(
    $app->extend('assetic.asset_manager', function($am, $app) {
        $am->set('styles', new Assetic\Asset\AssetCache(
            new Assetic\Asset\AssetCollection(array(
                new Assetic\Asset\FileAsset(__DIR__ . '/../web/less/styles.less', array($app['assetic.filter_manager']->get('lessphp'))),
                new Assetic\Asset\FileAsset(__DIR__ . '/../web/less/project.less',  array($app['assetic.filter_manager']->get('lessphp'))),
            )),
            new Assetic\Cache\FilesystemCache(__DIR__.'/../cache/assetic')
        ));
        $am->get('styles')->setTargetPath('compiled/styles.css');

        return $am;
    })
);

//
// Mail
//
$app->register(new Silex\Provider\SwiftmailerServiceProvider());

$app['swiftmailer.options'] = array(
    'host' => 'srvmail'
);

return $app;
