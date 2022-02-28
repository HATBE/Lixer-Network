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
    
                $_db->query("SELECT COUNT(id) as c FROM posts WHERE text LIKE CONCAT('%', :query, '%');");
                $_db->bind('query', $_searchQuery);
                $count = $_db->single()->c;
    
                if($count <= 0) {
                    DefaultResponse::_404NoItemsFound('posts');
                }
    
                $paging = new Paging($count, $_page, $_itemsPerPage);
    
                if(!$paging->pageExists()) {
                    DefaultResponse::_404PageNotFound();
                }
    
                $_db->query("SELECT * FROM posts WHERE text LIKE CONCAT('%', :query, '%') LIMIT :limit OFFSET :offset;");
                $_db->bind('query', $_searchQuery);
                $_db->bind('limit', $_itemsPerPage);
                $_db->bind('offset', $paging->getOffset());
                $res = $_db->resultSet();
    
                $rData = [];
    
                $rData['paging'] = $paging->getRData();
    
                foreach($res as $idx=>$item) {
                    $obj = new Post($_db, $item->id);
                    $rData['posts'][$idx] = $obj->getAsArray();
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
            $response->addMessage('A valid postid must be provided');
            $response->send();
            exit;
        }
        
        $_requestedPost = Post::getFromUid($_db, Sanitize::string($_url[0]));

        if(!$_requestedPost || !$_requestedPost->exists()) {
            DefaultResponse::_404ItemNotFound('user');
        }

        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            // == get a single post
            $rData = [];
            $rData = $_requestedPost->getAsArray();

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            // == update a post
            if(!$_loggedInUser) {
                DefaultResponse::_401NotAuthorized();
            }

            if($_loggedInUser->getId() !== $_requestedPost->getUser()->getId()) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('You cannot edit this post');
                $response->send();
                exit;
            }

            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawData)) {
                DefaultResponse::_400NotValidJson();
            }

            if(!isset($jsonData->text)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Please provide a text');
                $response->send();
                exit;
            }

            if(!Sanitize::checkStringBetween($jsonData->text, 3, 1024)) {
                DefaultResponse::_400WrongLengthString('3-1024');
            }

            if(!preg_match('/^[a-zA-Z0-9-_]{3,1024}$/', $jsonData->username)) {
                DefaultResponse::_400WrongFormatString('3-255');
            }

            $text = Sanitize::string($jsonData->text);

            $_db->query('UPDATE posts SET text = :text WHERE id LIKE :id;');
            $_db->bind('text', $text);
            $_db->bind('id', $post->getId());
            $_db->execute();

            $rData = [];
            $rData['post_id'] = $post->getUid();
            $rData['text'] = $text;

            $response = new JsonResponse();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // == delete a post
            if(!$_loggedInUser) {
                DefaultResponse::_401NotAuthorized();
            }

            if($_loggedInUser->getId() !== $_requestedPost->getUser()->getId()) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('You cannot edit this post');
                $response->send();
                exit;
            }

            $_db->query('DELETE FROM posts WHERE id LIKE :id;');
            $_db->bind('id', $post->getId());
            $_db->execute();

            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('Successfully deleted post');
            $response->send();
            exit;
        } else {
            DefaultResponse::_405RequestMethodNotAllowed();
        }
    } else { 
        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            // == get all posts
            $_db->query("SELECT COUNT(id) as c FROM posts;");
            $count = $_db->single()->c;

            if($count <= 0) {
                DefaultResponse::_404NoItemsFound('users');
            }

            $paging = new Paging($count, $_page, $_itemsPerPage);

            if(!$paging->pageExists()) {
                DefaultResponse::_404PageNotFound();
            }

            $_db->query("SELECT * FROM posts LIMIT :limit OFFSET :offset;");
            $_db->bind('limit', $_itemsPerPage);
            $_db->bind('offset', $paging->getOffset());
            $res = $_db->resultSet();

            $rData = [];

            $rData['paging'] = $paging->getRData();

            foreach($res as $idx=>$item) {
                $obj = new Post($_db, $item->id);
                $rData['posts'][$idx] = $obj->getAsArray();
            }

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // == create a post
            if(!$_loggedInUser) {
                DefaultResponse::_401NotAuthorized();
            }

            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawData)) {
                DefaultResponse::_400NotValidJson();
            }

            if(!isset($jsonData->text)) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('Please provide a text');
                $response->send();
                exit;
            }
            
            if(!Sanitize::checkStringBetween($jsonData->text, 3, 1024)) {
                DefaultResponse::_400WrongFormatString('3-1024');
            }

            if(!preg_match('/^[a-zA-Z0-9-_]{3,1024}$/', $jsonData->username)) {
                DefaultResponse::_400WrongFormatString('[a-zA-Z0-9-_]{3,1024}');
            }

            $text = Sanitize::string($jsonData->text);

            $uid;
            do {
                $uid = uniqid();
            } while(User::uidExists($_db, $uid));

            $_db->query('INSERT INTO posts (uid, user_id, text, time, type_id) VALUES (:uid, :user_id, :text, :time, :type_id);');
            $_db->bind('uid', $uid);
            $_db->bind('user_id', $userFromToken->getId());
            $_db->bind('text', $text);
            $_db->bind('time', time());
            $_db->bind('type_id', 1);
            $_db->execute();

            if($_db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("There was an error creating the post - please try again");
                $response->send();
                exit;
            }

            $rData = [];
            $rData['post_id'] = $uid;
            $rData['text'] = $text;
            $rData['type'] = 'plain';

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