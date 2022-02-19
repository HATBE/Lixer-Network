<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\JsonResponse;
    use app\DefaultResponse;
    use app\Sanitize;
    use app\users\User;

    if(isset($url[0])) {
        if(!Sanitize::checkInt($url[0])) {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('A valid sessionid must be provided');
            $response->send();
            exit;
        }

        $sessionId = Sanitize::int($url[0]);

        if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
            DefaultResponse::_401NotAuthorized();
        }

        $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

        if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // Logout
            $db->query('DELETE FROM sessions WHERE id LIKE :sessionid AND accesstoken LIKE :accesstoken;');
            $db->bind('sessionid', $sessionId);
            $db->bind('accesstoken', $accesstoken);
            $db->execute();

            if($db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Failed to log out of this session using access token provided");
                $response->send();
                exit;
            }

            $rData = [];
            $rData['session_id'] = $sessionId;

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            // Update Session
            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawPatchData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPatchData)) {
                DefaultResponse::_400NotValidJson();
            }

            if(!isset($jsonData->refreshtoken)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Please provide a refreshtoken');
                $response->send();
                exit;
            }

            $refreshtoken = $jsonData->refreshtoken;

            $db->query('SELECT id as sessionid, user_id as userid, accesstoken, refreshtoken, accesstokenexpiry, refreshtokenexpiry FROM sessions WHERE sessions.id LIKE :sessionid AND sessions.accesstoken LIKE :accesstoken AND sessions.refreshtoken LIKE :refreshtoken;');
            $db->bind('sessionid', $sessionId);
            $db->bind('accesstoken', $accesstoken);
            $db->bind('refreshtoken', $refreshtoken);
            $res = $db->single();

            if($db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Access or Refresh Token is incorrect for the session id");
                $response->send();
                exit;
            }

            if($res->refreshtokenexpiry < time()) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Refresh token has expired - please log in again");
                $response->send();
                exit;
            } 

            $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
            $refreshtoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

            $accesstokenExpiry = 1200; // 20 min
            $refreshtokenExpiry = 1209600; // 14 days

            $accesstokenExpiryDate = time() + $accesstokenExpiry;
            $refreshtokenExpiryDate = time() + $refreshtokenExpiry;

            $userId = $res->userid;
            $rSessionId = $res->sessionid;
            $rRefreshtoken = $res->refreshtoken;
            $rAccesstoken = $res->accesstoken;

            $db->query('UPDATE sessions SET accesstoken = :accesstoken, accesstokenexpiry = :accesstokenexpiry, refreshtoken = :refreshtoken, refreshtokenexpiry = :refreshtokenexpiry WHERE id LIKE :sessionid AND user_id LIKE :userid AND accesstoken LIKE :rAccesstoken AND refreshtoken LIKE :rRefreshtoken;');
            $db->bind('accesstoken', $accesstoken);
            $db->bind('accesstokenexpiry', $accesstokenExpiryDate);
            $db->bind('refreshtoken', $refreshtoken);
            $db->bind('refreshtokenexpiry', $refreshtokenExpiryDate);
            $db->bind('userid', $userId);
            $db->bind('sessionid', $rSessionId);
            $db->bind('rRefreshtoken', $rRefreshtoken);
            $db->bind('rAccesstoken', $rAccesstoken);
            $db->execute();

            if($db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Access token could not be refreshed - please log in again");
                $response->send();
                exit;
            }

            $rData = [];
            $rData['session_id'] = $rSessionId;
            $rData['access_token'] = $accesstoken;
            $rData['access_token_expires_in'] = $accesstokenExpiry;
            $rData['refresh_token'] = $refreshtoken;
            $rData['refresh_token_expires_in'] = $refreshtokenExpiry;

            $response = new JsonResponse();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else {
            DefaultResponse::_405RequestMethodNotAllowed();
        }
    } else {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Login
            sleep(1); // Login delay

            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)) {
                DefaultResponse::_400NotValidJson();
            }

            if(!isset($jsonData->username) || !isset($jsonData->password)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Please provide a username and a password');
                $response->send();
                exit;
            }

            if(!Sanitize::checkStringBetween($jsonData->username, 1, 255) || !Sanitize::checkStringBetween($jsonData->password, 1, 255)) {
                DefaultResponse::_400OverLengthString('1-255');
            }

            $username = Sanitize::string($jsonData->username);
            $password = $jsonData->password;

            $user = User::getFromUsername($db, $username);

            if($user === null || !$user->exists()) {
                DefaultResponse::_401UnameOrPwdNotValid();
            }

            if(!$user->verifyPassword($password)) {
                DefaultResponse::_401UnameOrPwdNotValid();
            }

            $userId = $user->getId();

            $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
            $refreshtoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

            $accesstokenExpiry = 1200; // 20 min
            $refreshtokenExpiry = 1209600; // 14 days

            $accesstokenExpiryDate = time() + $accesstokenExpiry;
            $refreshtokenExpiryDate = time() + $refreshtokenExpiry;

            $time = time();

            $db->query('INSERT INTO sessions (user_id, creationtime, accesstoken, accesstokenexpiry, refreshtoken, refreshtokenexpiry) VALUES (:user_id, :creationtime, :accesstoken, :accesstokenexpiry, :refreshtoken, :refreshtokenexpiry);');
            $db->bind('user_id', $userId);
            $db->bind('creationtime', $time);
            $db->bind('accesstoken', $accesstoken);
            $db->bind('accesstokenexpiry', $accesstokenExpiryDate);
            $db->bind('refreshtoken', $refreshtoken);
            $db->bind('refreshtokenexpiry', $refreshtokenExpiryDate);
            $db->execute();

            $sessionId = $db->lastId();

            $rData = [];
            $rData['session_id'] = $sessionId;
            $rData['access_token'] = $accesstoken;
            $rData['access_token_expires_in'] = $accesstokenExpiry;
            $rData['refresh_token'] = $refreshtoken;
            $rData['refresh_token_expires_in'] = $refreshtokenExpiry;

            $response = new JsonResponse();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else {
            DefaultResponse::_405RequestMethodNotAllowed();
        }
    }
    DefaultResponse::_404EndpointNotFound();