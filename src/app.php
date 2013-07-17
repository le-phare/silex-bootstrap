<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = require __DIR__.'/bootstrap.php';

$app->get('/{locale}', function($locale) use ($app) {
    $app['translator']->setLocale($locale);
    return $app['twig']->render('home.html.twig', array( 'locale' => $locale ));
})->value('locale', 'fr');;

$app->get('/{locale}/mail/{template}', function($locale, $template, Request $request) use ($app) {
    $app['translator']->setLocale($locale);
    $html = $app['twig']->render('Email/'.$template.'.html.twig', array( 'locale' => $locale, 'host' => $request->getSchemeAndHttpHost() ));

    // Extract subject from body
    $subject = 'Sujet manquant';
    if (preg_match('/<!-- *(.+) *-->/', $html, $matches)) {
        $subject = trim($matches[1]);
        if (empty($subject)) {
            $subject = 'Sujet vide';
        }
    }

    // Create the mail
    $message = \Swift_Message::newInstance()
        ->setSubject($subject)
        ->setFrom(array('noreply@lephare.com'))
        ->setTo(array($request->query->get('to')))
        ->setBody($html, 'text/html')
    ;

    // Send the mail
    $app['mailer']->send($message);

    return 'Mail envoyé a ' . $request->query->get('to');
})->assert('action', '.+');

$app->get('/{locale}/{controller}/{action}', function($locale,$controller,$action) use ($app){
    $app['translator']->setLocale($locale);
    return $app['twig']->render($controller.'/'.$action.'.html.twig', array( 'locale' => $locale ));
})->assert('action', '.+');


$app->error(function (NotFoundHttpException $e, $code) use ($app){
    return $app['twig']->render('error/404.html.twig');
});

return $app;
