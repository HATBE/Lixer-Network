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

    if(!isset($_POST['target'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Please provide a target!');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(empty($_POST['target'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Please provide a target!');
        $response->setData(null);
        $response->send();
        exit;
    }

    $target = Sanitize::int($_POST['target']);

    if(!User::existsId($db, $target)) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(404);
        $response->addMessage('Target user dows not exist!');
        $response->setData(null);
        $response->send();
        exit;
    }

    if($loggedInUser->isFollowing($target)) {
        User::unfollow($db, $loggedInUser->getId(), $target);
    } else {
        User::follow($db, $loggedInUser->getId(), $target);
    }

    $response->setSuccess(true);
    $response->setHttpStatusCode(200);
    $response->addMessage('performed successfully');
    $response->setData(null);
    $response->send();
    exit;