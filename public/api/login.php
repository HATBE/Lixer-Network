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

    if(User::isLoggedIn()) {
        $response->setSuccess(true);
        $response->setHttpStatusCode(200);
        $response->addMessage('You are already loggedin');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(!isset($_POST['username']) || !isset($_POST['password'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Please provide a username and a password!');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(empty($_POST['username']) || empty($_POST['password'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Please provide a username and a password!');
        $response->setData(null);
        $response->send();
        exit;
    }

    $username = Sanitize::string($_POST['username']);

    $user = User::getFromUsername($db, $username);

    if($user === null) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Username or pasword wrong!');
        $response->setData(null);
        $response->send();
        exit;
    }

    if(!$user->verifyPassword($_POST['password'])) {
        $response->setSuccess(false);
        $response->setHttpStatusCode(400);
        $response->addMessage('Username or pasword wrong!');
        $response->setData(null);
        $response->send();
        exit;
    }

    $_SESSION['loggedIn'] = $user->getId();

    $response->setSuccess(true);
    $response->setHttpStatusCode(200);
    $response->addMessage('Loggedin successfully');
    $response->setData(null);
    $response->send();
    exit;