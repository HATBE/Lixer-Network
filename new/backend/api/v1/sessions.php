<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\io\JsonResponse;
    use app\io\DefaultResponse;
    use app\Sanitize;
    use app\sessions\Session;
    use app\users\User;

    if(isset($_url[0])) {
        if(!Sanitize::checkInt($_url[0])) {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('A valid sessionid must be provided');
            $response->send();
            exit;
        }

        $_sessionId = Sanitize::string($_url[0]);
        $_requestedSessionId = Session::getFromUid($_db, $_sessionId);

        if(!$_loggedInUser) {
            DefaultResponse::_401NotAuthorized();
        }

        if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // == logout
            $_db->query('DELETE FROM sessions WHERE id LIKE :sessionid AND accesstoken LIKE :accesstoken;');
            $_db->bind('sessionid', $_sessionId);
            $_db->bind('accesstoken', $_accesstoken);
            $_db->execute();

            if($_db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Failed to log out of this session using access token provided");
                $response->send();
                exit;
            }

            $rData = [];
            $rData['session_id'] = $_sessionId;

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            // == update a session
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

            $_db->query('SELECT * FROM sessions WHERE id LIKE :sessionid AND accesstoken LIKE :accesstoken AND refreshtoken LIKE :refreshtoken;');
            $_db->bind('sessionid', $_sessionId);
            $_db->bind('accesstoken', $_accesstoken);
            $_db->bind('refreshtoken', $refreshtoken);
            $res = $_db->single();

            if($_db->rowCount() <= 0) {
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

            $rUserId = $res->userid;
            $rSessionId = $res->sessionid;
            $rRefreshtoken = $res->refreshtoken;
            $rAccesstoken = $res->accesstoken;

            $_db->query('UPDATE sessions SET accesstoken = :accesstoken, accesstokenexpiry = :accesstokenexpiry, refreshtoken = :refreshtoken, refreshtokenexpiry = :refreshtokenexpiry WHERE id LIKE :sessionid AND user_id LIKE :userid AND accesstoken LIKE :rAccesstoken AND refreshtoken LIKE :rRefreshtoken;');
            $_db->bind('accesstoken', $accesstoken);
            $_db->bind('accesstokenexpiry', $accesstokenExpiryDate);
            $_db->bind('refreshtoken', $refreshtoken);
            $_db->bind('refreshtokenexpiry', $refreshtokenExpiryDate);
            $_db->bind('userid', $rUserId);
            $_db->bind('sessionid', $rSessionId);
            $_db->bind('rRefreshtoken', $rRefreshtoken);
            $_db->bind('rAccesstoken', $rAccesstoken);
            $_db->execute();

            if($_db->rowCount() <= 0) {
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
            // == login
            sleep(1); // Login delay

            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawPatchData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPatchData)) {
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
                DefaultResponse::_400WrongFormatString('1-255');
            }

            $username = Sanitize::string($jsonData->username);
            $password = $jsonData->password;

            $user = User::getFromUsername($_db, $username);

            if($user === null || !$user->exists()) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Username or password invalid");
                $response->send();
                exit;
            }

            if(!$user->verifyPassword($password)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(401);
                $response->setSuccess(false);
                $response->addMessage("Username or password invalid");
                $response->send();
                exit;
            }

            $userId = $user->getId();

            $accesstoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());
            $refreshtoken = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)) . time());

            $accesstokenExpiry = 1200; // 20 min
            $refreshtokenExpiry = 1209600; // 14 days

            $accesstokenExpiryDate = time() + $accesstokenExpiry;
            $refreshtokenExpiryDate = time() + $refreshtokenExpiry;

            $time = time();

            $uid;
            do {
                $uid = uniqid();
            } while(Session::uidExists($_db, $uid));

            $_db->query('INSERT INTO sessions (uid, user_id, creationtime, accesstoken, accesstokenexpiry, refreshtoken, refreshtokenexpiry) VALUES (:uid, :user_id, :creationtime, :accesstoken, :accesstokenexpiry, :refreshtoken, :refreshtokenexpiry);');
            $_db->bind('uid', $uid);
            $_db->bind('user_id', $userId);
            $_db->bind('creationtime', $time);
            $_db->bind('accesstoken', $accesstoken);
            $_db->bind('accesstokenexpiry', $accesstokenExpiryDate);
            $_db->bind('refreshtoken', $refreshtoken);
            $_db->bind('refreshtokenexpiry', $refreshtokenExpiryDate);
            $_db->execute();

            $sessionId = $_db->lastId();

            $rData = [];
            $rData['session_id'] = $uid;
            $rData['user_id'] = $user->getUid();
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