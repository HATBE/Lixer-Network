<?php
    require_once(__DIR__ . '/../../src/init.php');

    use app\DefaultResponse;
    use app\JsonResponse;
    use app\posts\Post;
    use app\users\User;
    use app\Sanitize;

    if(isset($url[0])) {
        if(!Sanitize::checkInt($url[0])) {
            $response = new JsonResponse();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage('A valid postid must be provided');
            $response->send();
            exit;
        }

        $postId = Sanitize::int($url[0]);

        $post = new Post($db, $postId);

        if($post === null || !$post->exists()) {
            DefaultResponse::_404NotFound('post');
        }

        if($_SERVER['REQUEST_METHOD'] === 'GET') {
            // get a single post
            $rData = [];
            $rData = $post->getAsArray();

            $response = new JsonResponse();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'PATCH') {
            // update a post
            if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                DefaultResponse::_401NotAuthorized();
            }

            $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

            if(!$id = User::isAuthorized($db, $accesstoken)) {
                DefaultResponse::_401NotAuthorized();
            }

            $userFromToken = new User($db, $id);

            if($userFromToken->getId() !== $post->getUser()->getId()) {
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

            $rawPatchData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPatchData)) {
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
                DefaultResponse::_400OverLengthString('3-1024');
            }

            $text = Sanitize::string($jsonData->text);

            $db->query('UPDATE posts SET text = :text WHERE id LIKE :id;');
            $db->bind('text', $text);
            $db->bind('id', $post->getId());
            $db->execute();

            $rData = [];
            $rData['post_id'] = $post->getId();
            $rData['text'] = $text;

            $response = new JsonResponse();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->setData($rData);
            $response->send();
            exit;
        } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            // delete a post
            if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                DefaultResponse::_401NotAuthorized();
            }

            $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

            if(!$id = User::isAuthorized($db, $accesstoken)) {
                DefaultResponse::_401NotAuthorized();
            }

            $userFromToken = new User($db, $id);

            if($userFromToken->getId() !== $post->getUser()->getId()) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage('You cannot delete this post');
                $response->send();
                exit;
            }

            $db->query('DELETE FROM posts WHERE id LIKE :id;');
            $db->bind('id', $post->getId());
            $db->execute();

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
            // get all posts
            $db->query('SELECT COUNT(id) as c FROM posts;');
            $postsCount = $db->single()->c;

            if($postsCount <= 0) {
                DefaultResponse::_404NoItemsFound('posts');
            }

            $maxPages = ceil($postsCount / $itemsPerPage);

            if($page > $maxPages || $page <= 0) {
                DefaultResponse::_404PageNotFound();
            }

            $offset = ($page == 1 ?  0 : ($itemsPerPage*($page-1)));

            $db->query('SELECT * FROM posts LIMIT :limit OFFSET :offset;');
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
        } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // create a post
            if(!isset($_SERVER['HTTP_AUTHORIZATION'])) {
                DefaultResponse::_401NotAuthorized();
            }

            $accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

            if(!$id = User::isAuthorized($db, $accesstoken)) {
                DefaultResponse::_401NotAuthorized();
            }

            $userFromToken = new User($db, $id);

            if(!isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                DefaultResponse::_400NotJson();
            }

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)) {
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
                DefaultResponse::_400OverLengthString('3-1024');
            }

            /*if(!preg_match('/^[a-zA-Z0-9-_]{3,1024}$/', $jsonData->username)) {
                DefaultResponse::_400OverLengthString('3-255');
            }*/

            $text = Sanitize::string($jsonData->text);

            $db->query('INSERT INTO posts (user_id, text, time, type_id) VALUES (:user_id, :text, :time, :type_id);');
            $db->bind('user_id', $userFromToken->getId());
            $db->bind('text', $text);
            $db->bind('time', time());
            $db->bind('type_id', 1);
            $db->execute();

            if($db->rowCount() <= 0) {
                $response = new JsonResponse();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("There was an error creating the post - please try again");
                $response->send();
                exit;
            }

            $postId = $db->lastId();

            $rData = [];
            $rData['post_id'] = $postId;
            $rData['text'] = $text;
            $rData['type'] = 1;

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