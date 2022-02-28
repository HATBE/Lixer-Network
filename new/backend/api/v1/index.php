<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\io\JsonResponse;

    $response = new JsonResponse();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage('Please read the documentation of this API!');
    $response->send();