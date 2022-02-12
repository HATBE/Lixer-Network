<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\JsonResponse;
    use app\Sanitize;
    use app\users\User;

    $response = new JsonResponse();

    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Request method not allowed');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(!User::isLoggedIn()) {
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('Please login!');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(!isset($_POST['text'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Please provide a text!');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(empty($_POST['text'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Please provide a text!');
        $response->setData(null);
        $response->send();
        exit;
    }

    $text = Sanitize::String($_POST['text']);

    if(strlen($text) >= 350 || strlen($text) <= 2) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Text must be between 3 and 350 chars!');
        $response->setData(null);
        $response->send();
        exit;
    }

    $id = $loggedInUser->getId();
    $time = time();

    $db->query("INSERT INTO textPosts (user_id, time, text) VALUES (:id, :time, :text);");
    $db->bind('id', $id);
    $db->bind('time', $time);
    $db->bind('text', $text);
    $db->execute();

    $lastId = $db->lastId();

    $data = ['id' => $lastId];

    $response->setSuccess(false);
    $response->setHttpStatusCode(201);
    $response->addMessage('Successfully posted!');
    $response->setData($data);
    $response->send();
    exit;