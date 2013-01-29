<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = require __DIR__.'/bootstrap.php';

$app->get('/{locale}', function($locale) use ($app) {
    $app['translator']->setLocale($locale);
    return $app['twig']->render('home.html.twig', array( 'locale' => $locale ));
})->value('locale', 'fr');;

$app->get('/{locale}/{controller}/{action}', function($locale,$controller,$action) use ($app){
    $app['translator']->setLocale($locale);
    return $app['twig']->render($controller.'/'.$action.'.html.twig', array( 'locale' => $locale ));
})->assert('action', '.+');

$app->error(function (NotFoundHttpException $e, $code) use ($app){
    return $app['twig']->render('error/404.html.twig');
});

return $app;
