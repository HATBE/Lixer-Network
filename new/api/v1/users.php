<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\JsonResponse;
    use app\DefaultResponse;
    use app\Sanitize;
    use app\users\User;

    $itemsPerPage = 1;

    $page = isset($_GET['page']) ? Sanitize::int($_GET['page']) : 1;

    if(isset($url[0])) {
        if(!Sanitize::checkInt($url[0])) {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('A valid sessionid must be provided');
            $response->send();
            exit;
        }

        $userId = Sanitize::int($url[0]);

        $user = new User($db, $userId);

        if($user === null || !$user->exists()) {
            DefaultResponse::_404NotFound('user');
        }

        if(isset($url[1])) {
            if($url[1] === 'follow') {
                // follow a user
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // TODO:
                } else {
                    DefaultResponse::_405RequestMethodNotAllowed();
                }
            } else if($url[1] === 'unfollow') {
                // unfollow a user
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // TODO:
                } else {
                    DefaultResponse::_405RequestMethodNotAllowed();
                }
            }
        } else {    
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                // get a user
                $rData = [];
                $rData['user_id'] = $user->getId();
                $rData['username'] = $user->getUsername();
    
                $response = new JsonResponse();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setData($rData);
                $response->send();
                exit;
            } if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                // update a user
                if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                    DefaultResponse::_401NotAuthorized();
                }
    
                $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];
    
                // TODO:
            } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                // TODO:
            } else {
                DefaultResponse::_405RequestMethodNotAllowed();
            }
        }
    } else {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            // get all users
            $db->query('SELECT COUNT(id) as c FROM users;');
            $usersCount = $db->single()->c;

            $maxPages = ceil($usersCount / $itemsPerPage);

            if($page > $maxPages || $page <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Page not found");
                $response->send();
                exit;
            }

            $offset = ($page == 1 ?  0 : ($itemsPerPage*($page-1)));

            $db->query('SELECT * FROM users LIMIT :limit OFFSET :offset;');
            $db->bind('limit', $itemsPerPage);
            $db->bind('offset', $offset);
            $res = $db->resultSet();

            $rData = [];

            $rData['rows_returned'] = $db->rowCount();
            $rData['total_rows'] = $usersCount;
            $rData['total_pages'] = $maxPages;

            foreach($res as $idx=>$user) {
               $userO = new User($db, $user);
               $rData[$idx]['user_id'] = $userO->getId();
               $rData[$idx]['username'] = $userO->getUsername();
            }

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Register
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

            if(User::usernameExists($db, $username)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('This username is already in use');
                $response->send();
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $db->query('INSERT INTO users (username, password) VALUES (:username, :password);');
            $db->bind('username', $username);
            $db->bind('password', $hashedPassword);
            $db->execute();

            if($db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("There was an error creating the user account - please try again");
                $response->send();
                exit;
            }

            $userId = $db->lastId();

            $rData = [];
            $rData['user_id'] = $userId;
            $rData['username'] = $username;

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else {
            DefaultResponse::_405RequestMethodNotAllowed();
        }
    }
    DefaultResponse::_404EndpointNotFound();