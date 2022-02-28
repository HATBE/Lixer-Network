<?php
    require_once(__DIR__ . '/../config/auth.php');
    require_once(__DIR__ . '/../config/config.php');
    require_once(__DIR__ . '/autoload.php');

    use app\io\Database;
    use app\io\JsonResponse;
    use app\io\DefaultResponse;
    use app\users\User;
    use app\Sanitize;

    // \/ CORS settings \/
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Methods: POST, DELETE, GET, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Origin: *');
        
        $response = new JsonResponse();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->send();
        exit;
    }
    // /\ CORS settings /\

    // \/ parse URL Params \/
    $_url = null;
    if(isset($_SERVER['PATH_INFO'])) {
        $_url = $_SERVER['PATH_INFO'];
        $_url = rtrim($_url, '/'); // remove last slash
        $_url = ltrim($_url, '/'); // remove first slash
        $_url = Sanitize::url($_url);
        $_url = explode('/', $_url); // split url in segments, by slash
        if(!str_contains(explode('/', substr(rtrim($_SERVER['REQUEST_URI']), 1))[0], '.php')) array_shift($_url); // if element 0 is a ".php"-file, remove it from array (this can happen, if the file is specificly requestet in the url)
        if(empty($_url) || $_url[0] == '') $_url = null; // if no path is requested, set url to null
    }
    // /\ parse URL Params /\

    $_db = new Database();

    // \/ get some provided data \/
    $_page = isset($_GET['page']) ? Sanitize::int($_GET['page']) : 1;
    $_itemsPerPage = isset($_GET['itemsPerPage']) ? max(min(intval(Sanitize::int($_GET['itemsPerPage'])), MAX_ITEMS_PER_PAGE), 1) : DEFAULT_ITEMS_PER_PAGE; // -> get a number between 1 and "maximum amount set in config" (!if isset, else, use "default value set in config")
    $_searchQuery = isset($_GET['q']) ? Sanitize::string($_GET['q']): null;
    // /\ get some provided data /\

    $_loggedInUser = false;
    $_accesstoken = null;
    
    if(isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $_accesstoken = $_SERVER['HTTP_AUTHORIZATION'];

        if(!$_loggedInUser = User::getFromAccesstoken($_db, $_accesstoken)) {
            DefaultResponse::_401NotAuthorized();
        }
    }