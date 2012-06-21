<?php

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = require __DIR__.'/bootstrap.php';

$app->get('/', function() use ($app){
    return $app['twig']->render('home.html.twig');
});

$app->get('/{controller}/{action}', function($controller,$action) use ($app){
    return $app['twig']->render($controller.'/'.$action.'.html.twig');
});

$app->error(function (NotFoundHttpException $e, $code) use ($app){
    return $app['twig']->render('error/404.html.twig');
});

return $app;