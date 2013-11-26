<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

$app = require __DIR__.'/bootstrap.php';

$webDir = __DIR__.'/../web';

// Home page
$app->get('/{locale}', function($locale) use ($app) {
    $app['translator']->setLocale($locale);
    return $app['twig']->render('home.html.twig', array( 'locale' => $locale ));
})->value('locale', 'fr');;

// Send an Email
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

// Create a PDF
$app->get('/{locale}/{controller}/{action}.pdf', function($locale,$controller,$action) use ($app) {
    $app['translator']->setLocale($locale);
    $html = $app['twig']->render($controller.'/'.$action.'.html.twig', array( 'locale' => $locale ));

    $output = tempnam(sys_get_temp_dir(), 'pdf_');
    $app['snappy.pdf']->generateFromHtml($html, $output, array(
        'margin-bottom' => 50,
        'footer-html' => $request->getSchemeAndHttpHost() . '/fr/Commons/footerPdf',
    ), true);

    $response = new Response(file_get_contents($output), 200, array(
        'Content-Type' =>'application/pdf',
        'Content-Disposition' => 'attachment; filename="'.$controller.'_'.$action.'.pdf"'
    ));

    return $response;

})->assert('action', '.+');

// Main route
$app->get('/{locale}/{controller}/{action}', function($locale,$controller,$action) use ($app){
    $app['translator']->setLocale($locale);
    return $app['twig']->render($controller.'/'.$action.'.html.twig', array( 'locale' => $locale ));
})->assert('action', '.+');

// Render an error page
$app->error(function (NotFoundHttpException $e, $code) use ($app){
    return $app['twig']->render('error/404.html.twig');
});

return $app;
