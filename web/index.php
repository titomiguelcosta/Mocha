<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tmc\Parser\JsonRequestParser;
use Tmc\Storage\FileStorage;

$app = new Silex\Application();
$app['debug'] = true;

$app->post('/prime/{token}', function(Request $request, $token) use ($app) {
    $responses = new JsonRequestParser($request->getContent());
    $storage = new FileStorage('/tmp');
    $storage->save($token, $responses->getJson());

    return new Response('', 200);
});

$app->match('/play/{token}', function(Request $request, $token) use ($app) {
    $storage = new FileStorage('/tmp');
    $json = $storage->play($token);

    $response = new Response($json['body'], $json['status_code'], $json['headers']);
    $response->setStatusCode($json['status_code'], $json['description']);

    return $response;
});

$app->post('/debug/{token}', function(Request $request, $token) use ($app) {
    
});

$app->run();
