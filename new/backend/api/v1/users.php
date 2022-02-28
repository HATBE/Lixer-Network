<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\io\DefaultResponse;
    use app\io\JsonResponse;
    use app\users\User;
    use app\posts\Post;
    use app\Paging;
    use app\Sanitize;

    if(isset($_url[0])) {
        // if a param isset
        if($_url[0] === 'search') {
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                // == search for a user by username
                if(!$_searchQuery) {
                    DefaultResponse::_400NotProvidedASearchQuery();
                }
    
                $_db->query("SELECT COUNT(id) as c FROM users WHERE username LIKE CONCAT('%', :query, '%');");
                $_db->bind('query', $_searchQuery);
                $count = $_db->single()->c;
    
                if($count <= 0) {
                    DefaultResponse::_404NoItemsFound('users');
                }
    
                $paging = new Paging($count, $_page, $_itemsPerPage);
    
                if(!$paging->pageExists()) {
                    DefaultResponse::_404PageNotFound();
                }
    
                $_db->query("SELECT * FROM users WHERE username LIKE CONCAT('%', :query, '%') LIMIT :limit OFFSET :offset;");
                $_db->bind('query', $_searchQuery);
                $_db->bind('limit', $_itemsPerPage);
                $_db->bind('offset', $paging->getOffset());
                $res = $_db->resultSet();
    
                $rData = [];
    
                $rData['paging'] = $paging->getRData();
    
                foreach($res as $idx=>$item) {
                    $obj = new User($_db, $item->id);
                    $rData['users'][$idx] = $obj->getAsArray();
                }
    
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

        if(!Sanitize::checkString($_url[0])) {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('A valid userid must be provided');
            $response->send();
            exit;
        }
        
        $_requestedUser = User::getFromUid($_db, Sanitize::string($_url[0]));

        if(!$_requestedUser || !$_requestedUser->exists()) {
            DefaultResponse::_404ItemNotFound('user');
        }

        if(isset($_url[1])) {
            if($_url[1] === 'follow') {
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // == follow a user
                    if(!$_loggedInUser) {
                        DefaultResponse::_401NotAuthorized();
                    }

                    if($_requestedUser->getId() === $_loggedInUser->getId()) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You cannot follow yourself');
                        $response->send();
                        exit;
                    }

                    if($_loggedInUser->isFollowing($_requestedUser->getId())) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You already follow this user');
                        $response->send();
                        exit;
                    }

                    $_db->query('INSERT INTO following (source_user_id, target_user_id, time) VALUES (:source, :target, :time);');
                    $_db->bind('source', $_loggedInUser->getId());
                    $_db->bind('target', $_requestedUser->getId());
                    $_db->bind('time', time());
                    $_db->execute();
                    
                    $response = new JsonResponse();
                    $response->setHttpStatusCode(201);
                    $response->setSuccess(false);
                    $response->addMessage('Followed successfully');
                    $response->send();
                    exit;
                } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    // == unfollow a user
                    if(!$_loggedInUser) {
                        DefaultResponse::_401NotAuthorized();
                    }

                    if($_requestedUser->getId() === $_loggedInUser->getId()) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You cannot unfollow yourself');
                        $response->send();
                        exit;
                    }

                    if(!$_loggedInUser->isFollowing($_requestedUser->getId())) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You are not follow this user');
                        $response->send();
                        exit;
                    }

                    $_db->query('DELETE FROM following WHERE source_user_id LIKE :source AND target_user_id LIKE :target;');
                    $_db->bind('source', $userFromToken->getId());
                    $_db->bind('target', $user->getId());
                    $_db->execute();

                    $response = new JsonResponse();
                    $response->setHttpStatusCode(201);
                    $response->setSuccess(false);
                    $response->addMessage('Unfollowed successfully');
                    $response->send();
                    exit;  
                } else {
                    DefaultResponse::_405RequestMethodNotAllowed();
                }
            } else if($_url[1] === 'followers') {
                if($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // == get all followers from a user
                    $_db->query("SELECT COUNT(id) as c FROM following WHERE target_user_id LIKE :id;");
                    $_db->bind('id', $_requestedUser->getId());
                    $count = $_db->single()->c;
        
                    if($count <= 0) {
                        DefaultResponse::_404NoItemsFound('users');
                    }
        
                    $paging = new Paging($count, $_page, $_itemsPerPage);
        
                    if(!$paging->pageExists()) {
                        DefaultResponse::_404PageNotFound();
                    }
        
                    $_db->query("SELECT * FROM following WHERE target_user_id LIKE :id LIMIT :limit OFFSET :offset;");
                    $_db->bind('id', $_requestedUser->getId());
                    $_db->bind('limit', $_itemsPerPage);
                    $_db->bind('offset', $paging->getOffset());
                    $res = $_db->resultSet();
        
                    $rData = [];
        
                    $rData['paging'] = $paging->getRData();
        
                    foreach($res as $idx=>$item) {
                        $obj = new User($_db, $item->source_user_id);
                        $rData['users'][$idx] = $obj->getAsArray();
                        $rData['users'][$idx]['since'] = intval($item->time);
                    }
        
                    $response = new JsonResponse();
                    $response->setHttpStatusCode(200);
                    $response->setSuccess(true);
                    $response->setData($rData);
                    $response->send();
                    exit;
                } else {
                    DefaultResponse::_405RequestMethodNotAllowed();
                }
            } else if($_url[1] === 'following') {
                if($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // == get all users this user follows
                    $_db->query("SELECT COUNT(id) as c FROM following WHERE source_user_id LIKE :id;");
                    $_db->bind('id', $_requestedUser->getId());
                    $count = $_db->single()->c;
        
                    if($count <= 0) {
                        DefaultResponse::_404NoItemsFound('users');
                    }
        
                    $paging = new Paging($count, $_page, $_itemsPerPage);
        
                    if(!$paging->pageExists()) {
                        DefaultResponse::_404PageNotFound();
                    }
        
                    $_db->query("SELECT * FROM following WHERE source_user_id LIKE :id LIMIT :limit OFFSET :offset;");
                    $_db->bind('id', $_requestedUser->getId());
                    $_db->bind('limit', $_itemsPerPage);
                    $_db->bind('offset', $paging->getOffset());
                    $res = $_db->resultSet();
        
                    $rData = [];
        
                    $rData['paging'] = $paging->getRData();
        
                    foreach($res as $idx=>$item) {
                        $obj = new User($_db, $item->target_user_id);
                        $rData['users'][$idx] = $obj->getAsArray();
                        $rData['users'][$idx]['since'] = intval($item->time);
                    }
        
                    $response = new JsonResponse();
                    $response->setHttpStatusCode(200);
                    $response->setSuccess(true);
                    $response->setData($rData);
                    $response->send();
                    exit;
                } else {
                    DefaultResponse::_405RequestMethodNotAllowed();
                }
            } else if($_url[1] === 'posts') {
                // == get all posts of a user
                $_db->query("SELECT COUNT(id) as c FROM posts WHERE user_id LIKE :id;");
                $_db->bind('id', $_requestedUser->getId());
                $count = $_db->single()->c;
    
                if($count <= 0) {
                    DefaultResponse::_404NoItemsFound('posts');
                }
    
                $paging = new Paging($count, $_page, $_itemsPerPage);
    
                if(!$paging->pageExists()) {
                    DefaultResponse::_404PageNotFound();
                }
    
                $_db->query("SELECT * FROM posts WHERE user_id LIKE :id LIMIT :limit OFFSET :offset;");
                $_db->bind('id', $_requestedUser->getId());
                $_db->bind('limit', $_itemsPerPage);
                $_db->bind('offset', $paging->getOffset());
                $res = $_db->resultSet();
    
                $rData = [];
    
                $rData['paging'] = $paging->getRData();
    
                foreach($res as $idx=>$item) {
                    $obj = new Post($_db, $item->id);
                    $rData['users'][$idx] = $obj->getAsArray();
                }
    
                $response = new JsonResponse();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setData($rData);
                $response->send();
                exit;
            }
        } else {
            // if no param isset
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                // == get a single user
                $rData = [];
                $rData = $_requestedUser->getAsArray();
    
                $response = new JsonResponse();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setData($rData);
                $response->send();
                exit;
            } else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                // == update a user

                // Todo:
                echo "todo";
                exit;
            } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                // == delete a user
                if(!$_loggedInUser) {
                    DefaultResponse::_401NotAuthorized();
                }

                if($_requestedUser->getId() !== $_loggedInUser->getId()) {
                    $response = new JsonResponse();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage('You cannot delete this user');
                    $response->send();
                    exit;
                }

                // delete all sessions from this user
                $_db->query('DELETE FROM sessions WHERE user_id LIKE :id;');
                $_db->bind('id', $_requestedUser->getId());
                $_db->execute();

                // delete all follows and followings from user
                $_db->query('DELETE FROM following WHERE source_user_id LIKE :id or target_user_id LIKE :id;');
                $_db->bind('id', $_requestedUser->getId());
                $_db->execute();

                // delete all posts from user
                $_db->query('DELETE FROM posts WHERE user_id LIKE :id;');
                $_db->bind('id', $_requestedUser->getId());
                $_db->execute();

                // delete user
                $_db->query('DELETE FROM users WHERE id LIKE :id;');
                $_db->bind('id', $_requestedUser->getId());
                $_db->execute();
                
                $response = new JsonResponse();
                $response->setHttpStatusCode(200);
                $response->setSuccess(false);
                $response->addMessage('Successfully deleted user');
                $response->send();
                exit;
            } else {
                DefaultResponse::_405RequestMethodNotAllowed();
            }
        }
    } else {
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            // == get all users
            $_db->query("SELECT COUNT(id) as c FROM users;");
            $count = $_db->single()->c;

            if($count <= 0) {
                DefaultResponse::_404NoItemsFound('users');
            }

            $paging = new Paging($count, $_page, $_itemsPerPage);

            if(!$paging->pageExists()) {
                DefaultResponse::_404PageNotFound();
            }

            $_db->query("SELECT * FROM users LIMIT :limit OFFSET :offset;");
            $_db->bind('limit', $_itemsPerPage);
            $_db->bind('offset', $paging->getOffset());
            $res = $_db->resultSet();

            $rData = [];

            $rData['paging'] = $paging->getRData();

            foreach($res as $idx=>$item) {
                $obj = new User($_db, $item->id);
                $rData['users'][$idx] = $obj->getAsArray();
            }

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // == register a user account
            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawData)) {
                DefaultResponse::_400NotValidJson();
            }

            if(!isset($jsonData->username) || !isset($jsonData->password)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Please provide a username and password');
                $response->send();
                exit;
            }

            if(!Sanitize::checkStringBetween($jsonData->username, 3, 16) || !Sanitize::checkStringBetween($jsonData->password, 3, 16)) {
                DefaultResponse::_400WrongLengthString('3-16');
            }

            if(!preg_match('/^[a-zA-Z0-9-_]{3,16}$/', $jsonData->username)) {
                DefaultResponse::_400WrongFormatString('A-Za-z0-9-_{3,16}');
            }

            $username = Sanitize::string($jsonData->username);
            $password = $jsonData->password;

            if(User::usernameExists($_db, $username)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('This username is already in use');
                $response->send();
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $uid;
            do {
                $uid = uniqid();
            } while(User::uidExists($_db, $uid));

            $_db->query('INSERT into users (uid, username, password, joinTime) VALUES (:uid, :username, :password, :joinTime);');
            $_db->bind('uid', $uid);
            $_db->bind('joinTime', time());
            $_db->bind('username', $username);
            $_db->bind('password', $hashedPassword);
            $_db->execute();

            if($_db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("There was an error creating the user account - please try again");
                $response->send();
                exit;
            }

            $rData = [];
            $rData['user_id'] = $uid;
            $rData['username'] = $username;

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