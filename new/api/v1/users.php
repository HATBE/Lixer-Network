<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\JsonResponse;
    use app\DefaultResponse;
    use app\Sanitize;
    use app\users\User;
    use app\posts\Post;

    if(isset($url[0])) {
        if(!Sanitize::checkInt($url[0])) {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('A valid userid must be provided');
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
                if($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // follow a user
                    if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                        DefaultResponse::_401NotAuthorized();
                    }
        
                    $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

                    if(!$id = User::isAuthorized($db, $accesstoken)) {
                        DefaultResponse::_401NotAuthorized();
                    }
    
                    $userFromToken = new User($db, $id);

                    if($userFromToken->getId() == $user->getId()) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You cannot follow yourself');
                        $response->send();
                        exit;
                    }

                    if($userFromToken->isFollowing($user)) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You already follow this user');
                        $response->send();
                        exit;
                    }

                    $db->query('INSERT INTO following (source_user_id, target_user_id, time) VALUES (:source, :target, :time);');
                    $db->bind('source', $userFromToken->getId());
                    $db->bind('target', $user->getId());
                    $db->bind('time', time());
                    $db->execute();

                    $response = new JsonResponse();
                    $response->setHttpStatusCode(201);
                    $response->setSuccess(false);
                    $response->addMessage('Followed successfully');
                    $response->send();
                    exit;
                } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                    // unfollow a user
                    if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                        DefaultResponse::_401NotAuthorized();
                    }
        
                    $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

                    if(!$id = User::isAuthorized($db, $accesstoken)) {
                        DefaultResponse::_401NotAuthorized();
                    }

                    $userFromToken = new User($db, $id);

                    if($userFromToken->getId() == $user->getId()) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You cannot unfollow yourself');
                        $response->send();
                        exit;
                    }

                    if(!$userFromToken->isFollowing($user)) {
                        $response = new JsonResponse();
                        $response->setHttpStatusCode(400);
                        $response->setSuccess(false);
                        $response->addMessage('You are not following this user');
                        $response->send();
                        exit;
                    }

                    $db->query('DELETE FROM following WHERE source_user_id LIKE :source AND target_user_id LIKE :target;');
                    $db->bind('source', $userFromToken->getId());
                    $db->bind('target', $user->getId());
                    $db->execute();

                    $response = new JsonResponse();
                    $response->setHttpStatusCode(201);
                    $response->setSuccess(false);
                    $response->addMessage('Unfollowed successfully');
                    $response->send();
                    exit;  
                } else {
                    DefaultResponse::_405RequestMethodNotAllowed();
                }
            } else if($url[1] === 'followers') {
                if($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // get all follower users from a user
                    $db->query('SELECT COUNT(id) as c FROM following WHERE target_user_id LIKE :id;');
                    $db->bind('id', $user->getId());
                    $usersCount = $db->single()->c;

                    if($usersCount <= 0) {
                        DefaultResponse::_404NoItemsFound('users');
                    }

                    $maxPages = ceil($usersCount / $itemsPerPage);

                    if($page > $maxPages || $page <= 0) {
                        DefaultResponse::_404PageNotFound();
                    }

                    $offset = ($page == 1 ?  0 : ($itemsPerPage*($page-1)));
        
                    $db->query('SELECT * FROM following WHERE target_user_id LIKE :id LIMIT :limit OFFSET :offset;');
                    $db->bind('id', $user->getId());
                    $db->bind('limit', $itemsPerPage);
                    $db->bind('offset', $offset);
                    $res = $db->resultSet();

                    $rData = [];

                    $rData['rows_returned'] = $db->rowCount();
                    $rData['total_rows'] = intval($usersCount);
                    $rData['total_pages'] = $maxPages;
                    $rData['has_next_page'] = $page >= $maxPages ? false : true;
                    $rData['has_last_page'] = $page >= 2 ? true : false;

                    foreach($res as $idx=>$user) {
                        $userO = new User($db, $user->source_user_id);
                        $rData['users'][$idx] = $userO->getAsArray();
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
            } else if($url[1] === 'following') {
                if($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // get all following users from a user
                    $db->query('SELECT COUNT(id) as c FROM following WHERE source_user_id LIKE :id;');
                    $db->bind('id', $user->getId());
                    $usersCount = $db->single()->c;

                    if($usersCount <= 0) {
                        DefaultResponse::_404NoItemsFound('users');
                    }

                    $maxPages = ceil($usersCount / $itemsPerPage);

                    if($page > $maxPages || $page <= 0) {
                        DefaultResponse::_404PageNotFound();
                    }

                    $offset = ($page == 1 ?  0 : ($itemsPerPage*($page-1)));
        
                    $db->query('SELECT * FROM following WHERE source_user_id LIKE :id LIMIT :limit OFFSET :offset;');
                    $db->bind('id', $user->getId());
                    $db->bind('limit', $itemsPerPage);
                    $db->bind('offset', $offset);
                    $res = $db->resultSet();

                    $rData = [];

                    $rData['rows_returned'] = $db->rowCount();
                    $rData['total_rows'] = intval($usersCount);
                    $rData['total_pages'] = $maxPages;
                    $rData['has_next_page'] = $page >= $maxPages ? false : true;
                    $rData['has_last_page'] = $page >= 2 ? true : false;

                    foreach($res as $idx=>$user) {
                        $userO = new User($db, $user->target_user_id);
                        $rData['users'][$idx] = $userO->getAsArray();
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
            } else if($url[1] === 'posts') {
                if($_SERVER['REQUEST_METHOD'] === 'GET') {
                    // get all posts from a user
                    $db->query('SELECT COUNT(id) as c FROM posts WHERE user_id LIKE :id;');
                    $db->bind('id', $user->getId());
                    $postsCount = $db->single()->c;

                    if($postsCount <= 0) {
                        DefaultResponse::_404NoItemsFound('post');
                    }

                    $maxPages = ceil($postsCount / $itemsPerPage);

                    if($page > $maxPages || $page <= 0) {
                        DefaultResponse::_404PageNotFound();
                    }

                    $offset = ($page == 1 ?  0 : ($itemsPerPage*($page-1)));
        
                    $db->query('SELECT * FROM posts WHERE user_id LIKE :id LIMIT :limit OFFSET :offset;');
                    $db->bind('id', $user->getId());
                    $db->bind('limit', $itemsPerPage);
                    $db->bind('offset', $offset);
                    $res = $db->resultSet();

                    $rData = [];

                    $rData['rows_returned'] = $db->rowCount();
                    $rData['total_rows'] = intval($postsCount);
                    $rData['total_pages'] = $maxPages;
                    $rData['has_next_page'] = $page >= $maxPages ? false : true;
                    $rData['has_last_page'] = $page >= 2 ? true : false;

                    foreach($res as $idx=>$post) {
                        $postO = new Post($db, $post->id);
                        $rData['posts'][$idx] = $postO->getAsArray();
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
        } else {    
            if($_SERVER['REQUEST_METHOD'] === 'GET') {
                // get a single user
                $rData = [];
                $rData = $user->getAsArray();
    
                $response = new JsonResponse();
                $response->setHttpStatusCode(200);
                $response->setSuccess(true);
                $response->setData($rData);
                $response->send();
                exit;
            } if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
                // update a user

                // TODO:
            } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                // delete a user
                if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                    DefaultResponse::_401NotAuthorized();
                }
    
                $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

                if(!$id = User::isAuthorized($db, $accesstoken)) {
                    DefaultResponse::_401NotAuthorized();
                }

                $userFromToken = new User($db, $id);

                if($userFromToken->getId() !== $user->getId()) {
                    $response = new JsonResponse();
                    $response->setHttpStatusCode(400);
                    $response->setSuccess(false);
                    $response->addMessage('You cannot delete this user');
                    $response->send();
                    exit;
                }

                $db->query('DELETE FROM sessions WHERE user_id LIKE :id;');
                $db->bind('id', $userFromToken->getId());
                $db->execute();

                $db->query('DELETE FROM following WHERE source_user_id LIKE :id or target_user_id LIKE :id;');
                $db->bind('id', $userFromToken->getId());
                $db->execute();

                $db->query('DELETE FROM posts WHERE user_id LIKE :id;');
                $db->bind('id', $userFromToken->getId());
                $db->execute();

                $db->query('DELETE FROM users WHERE id LIKE :id;');
                $db->bind('id', $userFromToken->getId());
                $db->execute();
                
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
            // get all users
            $db->query('SELECT COUNT(id) as c FROM users;');
            $usersCount = $db->single()->c;

            if($usersCount <= 0) {
                DefaultResponse::_404NoItemsFound('users');
            }

            $maxPages = ceil($usersCount / $itemsPerPage);

            if($page > $maxPages || $page <= 0) {
                DefaultResponse::_404PageNotFound();
            }

            $offset = ($page == 1 ?  0 : ($itemsPerPage*($page-1)));

            $db->query('SELECT * FROM users LIMIT :limit OFFSET :offset;');
            $db->bind('limit', $itemsPerPage);
            $db->bind('offset', $offset);
            $res = $db->resultSet();

            $rData = [];

            $rData['rows_returned'] = $db->rowCount();
            $rData['total_rows'] = intval($usersCount);
            $rData['total_pages'] = $maxPages;
            $rData['has_next_page'] = $page >= $maxPages ? false : true;
            $rData['has_last_page'] = $page >= 2 ? true : false;

            foreach($res as $idx=>$user) {
               $userO = new User($db, $user->id);
               $rData['users'][$idx] = $userO->getAsArray();
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

            if(!Sanitize::checkStringBetween($jsonData->username, 3, 16) || !Sanitize::checkStringBetween($jsonData->password, 3, 16)) {
                DefaultResponse::_400OverLengthString('3-255');
            }

            if(!preg_match('/^[a-zA-Z0-9-_]{3,16}$/', $jsonData->username)) {
                DefaultResponse::_400OverLengthString('3-255');
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

            $db->query('INSERT INTO users (username, password, joinTime) VALUES (:username, :password, :time);');
            $db->bind('time', time());
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